<script>
    const statusMap = {!! json_encode(\App\Helpers\OrderStatus::statuses()) !!};

    // Shared modal state
    let _courierModalOrderId = null;
    let _courierModalTrackingId = null;
    let _courierModalHasCourier = false;
    let _cachedCouriers = null;
    let _restaurantLat = null;
    let _restaurantLng = null;
    let _cancelModalOrderId = null;
    let _cancelModalTrackingId = null;
    let _cancelModalPlatform = null;

    // Merkezi backdrop temizlik fonksiyonu
    function cleanupModalBackdrop() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();

        // Tüm shared modallar kapandığında backdrop'u temizle
        ['sharedCourierModal', 'sharedCancelModal', 'OrdersModal', 'dateModal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('hidden.bs.modal', cleanupModalBackdrop);
            }
        });
    });

    function fetchOrders(status) {
        $.ajax({
            url: '/{{$key}}/orders/ajax',
            method: 'GET',
            success: function (data) {
                Object.keys(statusMap).forEach(status => updateTableForStatus(status));

                if (data) {
                    Object.keys(data).forEach(statusKey => {
                        data[statusKey].forEach(order => {
                            refreshOrderTable(order);
                        });
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Siparişleri alırken hata:', error);
            }
        });
    }

    // Pusher ayarları
    Pusher.logToConsole = false;
    var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

    let keyId = "{{ auth($key)->id() }}";
    var channel = pusher.subscribe(`{{$key}}-${keyId}`);

    channel.bind('new-order', function (data) {
        console.log('Gelen data:', data);
        if (data.order) {
            refreshOrderTable(data.order, true);

            if ('{{\App\Helpers\OrdersHelper::getOrderSystem(1)}}') {
                const audio = new Audio('{{asset('voices/order/beep-warning-6387.mp3')}}');
                audio.play().catch(err => {
                    console.error("Ses çalma başarısız:", err);
                });

                const newOrderEl = document.getElementById('newOrder');
                newOrderEl.style.display = 'block';
                newOrderEl.classList.add('blink');

                setTimeout(() => {
                    newOrderEl.style.display = 'none';
                    newOrderEl.classList.remove('blink');
                }, 3000);
            }
        }
    });

    channel.bind('update-order', function (data) {
        console.log('Gelen data:', data);
        if (data.order) {
            refreshOrderTable(data.order, true);
        }
    });

    // ==========================================
    // SHARED COURIER MODAL
    // ==========================================

    function haversineJs(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function showCourierPreview(courierId) {
        const courier = (_cachedCouriers || []).find(c => c.id == courierId);
        if (!courier) return;

        const statusLabels = {
            'active':   { dot: '#10b981', text: 'Müsait' },
            'service':  { dot: '#3b82f6', text: 'Serviste' },
            'passive':  { dot: '#ef4444', text: 'Pasif' },
            'break':    { dot: '#f59e0b', text: 'Molada' },
            'handover': { dot: '#8b5cf6', text: 'Yolda' }
        };
        const st = statusLabels[courier.status] || { dot: '#cbd5e1', text: 'Bilinmiyor' };

        let distanceHtml = '<span style="color:#94a3b8;font-size:13px;">Konum yok</span>';
        if (courier.latitude && courier.longitude && _restaurantLat && _restaurantLng) {
            const dist = haversineJs(
                parseFloat(courier.latitude), parseFloat(courier.longitude),
                parseFloat(_restaurantLat), parseFloat(_restaurantLng)
            );
            const distText = dist >= 1 ? dist.toFixed(2) + ' km' : (dist * 1000).toFixed(0) + ' m';
            const distColor = dist <= 3 ? '#16a34a' : dist <= 8 ? '#ca8a04' : '#dc2626';
            distanceHtml = `<span style="color:${distColor};font-size:16px;font-weight:800;">${distText}</span>`;
        }

        const activeOrders = courier.active_order_count || 0;

        document.getElementById('courierListContainer').innerHTML = `
            <button onclick="goBackToCourierList()" style="background:none;border:none;color:#6b7280;font-size:12px;font-weight:700;cursor:pointer;padding:0 0 14px 0;display:flex;align-items:center;gap:6px;">
                &#8592; Listeye Dön
            </button>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div style="width:48px;height:48px;background:#4f46e5;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:800;flex-shrink:0;">
                        ${courier.name ? courier.name.charAt(0).toUpperCase() : '?'}
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:800;color:#0f172a;">${escapeHtml(courier.name)}</div>
                        <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                            <span style="width:8px;height:8px;background:${st.dot};border-radius:50%;display:inline-block;"></span>
                            <span style="font-size:11px;font-weight:700;color:#94a3b8;">${st.text}</span>
                        </div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
                        <div style="font-size:9px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Size Uzaklığı</div>
                        <div style="margin-top:3px;">${distanceHtml}</div>
                    </div>
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
                        <div style="font-size:9px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Aktif Sipariş</div>
                        <div style="font-size:16px;font-weight:800;color:${activeOrders > 0 ? '#d97706' : '#0f172a'};margin-top:3px;">${activeOrders}</div>
                    </div>
                    ${courier.phone ? `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
                        <div style="font-size:9px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Telefon</div>
                        <div style="font-size:12px;font-weight:700;color:#0f172a;margin-top:3px;">${escapeHtml(courier.phone)}</div>
                    </div>` : ''}
                    ${courier.vehicle_type ? `<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
                        <div style="font-size:9px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;">Araç</div>
                        <div style="font-size:12px;font-weight:700;color:#0f172a;margin-top:3px;">${escapeHtml(courier.vehicle_type)}</div>
                    </div>` : ''}
                </div>
            </div>
            <button onclick="assignCourierToOrder(${courier.id})"
                style="width:100%;padding:14px;background:#4f46e5;color:#fff;border:none;border-radius:14px;font-size:13px;font-weight:800;cursor:pointer;letter-spacing:.3px;"
                onmouseover="this.style.background='#3730a3'" onmouseout="this.style.background='#4f46e5'">
                KURYE ATA &mdash; ${escapeHtml(courier.name)}
            </button>`;
    }

    function goBackToCourierList() {
        if (_cachedCouriers) renderCourierList(_cachedCouriers);
    }

    async function openCourierModal(orderId, trackingId, hasCourier, restaurantLat, restaurantLng) {
        _courierModalOrderId = orderId;
        _courierModalTrackingId = trackingId;
        _courierModalHasCourier = hasCourier;
        _restaurantLat = restaurantLat || null;
        _restaurantLng = restaurantLng || null;

        // Modal bilgilerini güncelle
        document.getElementById('courierModalOrderInfo').textContent = `Sipariş #${trackingId}`;

        // Boşa çıkar butonunu göster/gizle
        const removeBtn = document.getElementById('courierRemoveBtn');
        if (hasCourier) {
            removeBtn.classList.remove('d-none');
        } else {
            removeBtn.classList.add('d-none');
        }

        // Arama alanını temizle
        document.getElementById('courierSearchInput').value = '';

        // Kurye listesini yükle
        const container = document.getElementById('courierListContainer');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                <p class="text-muted fw-bold mt-2" style="font-size: 11px;">Kuryeler yükleniyor...</p>
            </div>`;

        // Modalı aç — body'ye taşı (aria-hidden çakışmasını önler)
        const modalEl = document.getElementById('sharedCourierModal');
        if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) modal = new bootstrap.Modal(modalEl, { backdrop: false });
        modal.show();

        try {
            _cachedCouriers = await fetchCouriers();
            renderCourierList(_cachedCouriers);
        } catch (err) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 1.5rem;"></i>
                    <p class="text-danger fw-bold" style="font-size: 11px;">Kuryeler yüklenemedi</p>
                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="openCourierModal('${orderId}', '${trackingId}', ${hasCourier})">Tekrar Dene</button>
                </div>`;
        }
    }

    function renderCourierList(couriers) {
        const container = document.getElementById('courierListContainer');

        if (!couriers || couriers.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-user-slash text-muted mb-2" style="font-size: 1.5rem;"></i>
                    <p class="text-muted fw-bold" style="font-size: 11px;">Kayıtlı kurye bulunamadı</p>
                </div>`;
            return;
        }

        let html = '';
        couriers.forEach(courier => {
            const activeOrders = courier.active_order_count || 0;
            const statusMap = {
                'active': { dot: '#10b981', bg: '#4f46e5', text: 'Müsait' },
                'service': { dot: '#3b82f6', bg: '#6366f1', text: 'Serviste' },
                'passive': { dot: '#ef4444', bg: '#94a3b8', text: 'Pasif' },
                'break': { dot: '#f59e0b', bg: '#64748b', text: 'Molada' },
                'handover': { dot: '#8b5cf6', bg: '#4338ca', text: 'Yolda' }
            };

            const currentStatus = statusMap[courier.status] || { dot: '#cbd5e1', bg: '#94a3b8', text: 'Bilinmiyor' };

            const dotColor = currentStatus.dot;
            const statusText = currentStatus.text;
            const avatarBg = currentStatus.bg;

            html += `
    <div class="d-flex align-items-center justify-content-between p-3 mb-2 bg-white border rounded-3" style="cursor:pointer; transition:all 0.2s; border-color:#e2e8f0;" onclick="showCourierPreview(${courier.id})" onmouseover="this.style.borderColor='#4f46e5';this.style.boxShadow='0 2px 8px rgba(79,70,229,0.1)'" onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center text-white" style="width:38px;height:38px;background:${avatarBg};border-radius:12px;font-size:14px;font-weight:800;">
                ${courier.name ? courier.name.charAt(0).toUpperCase() : '?'}
            </div>
            <div>
                <div style="font-size:13px; font-weight:800; color:#0f172a;">${courier.name}</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    <span style="width:7px;height:7px;background:${dotColor};border-radius:50%;display:inline-block;"></span>
                    <span style="font-size:10px; font-weight:700; color:#94a3b8;">${statusText}</span>
                    ${activeOrders > 0 ? `<span style="font-size:9px; font-weight:800; color:#d97706; background:#fef3c7; padding:1px 6px; border-radius:6px; margin-left:4px;">${activeOrders} sipariş</span>` : ''}
                </div>
            </div>
        </div>
        <i class="fas fa-arrow-right" style="font-size:9px; color:#cbd5e1;"></i>
    </div>`;
        });

        container.innerHTML = html;
    }

    function filterCourierList() {
        const query = document.getElementById('courierSearchInput').value.toLowerCase().trim();
        if (!_cachedCouriers) return;

        if (!query) {
            renderCourierList(_cachedCouriers);
            return;
        }

        const filtered = _cachedCouriers.filter(c => c.name && c.name.toLowerCase().includes(query));
        renderCourierList(filtered);
    }

    function assignCourierToOrder(courierId) {
        if (!_courierModalOrderId) return;

        const container = document.getElementById('courierListContainer');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm" role="status" style="color: #4f46e5;"></div>
                <p class="fw-bold mt-2" style="font-size: 12px; color: #4f46e5;">Kurye atanıyor...</p>
            </div>`;

        $.ajax({
            type: 'GET',
            url: '/{{$key}}/orders/sendCourier/' + _courierModalOrderId + '/' + courierId,
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // Modalı kapat
                    const modalEl = document.getElementById('sharedCourierModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                    setTimeout(() => {
                        cleanupModalBackdrop();
                        fetchOrders();
                    }, 150);

                    Swal.fire({
                        title: data.message ? data.message.toUpperCase() : 'KURYE ATANDI',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        background: '#ffffff',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800 !text-xl'
                        }
                    });
                }
            },
            error: function (xhr) {
                let errorMsg = 'İşlem sırasında bir hata oluştu!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                // Listeyi tekrar göster
                if (_cachedCouriers) renderCourierList(_cachedCouriers);

                Swal.fire({
                    title: 'HATA',
                    text: errorMsg,
                    icon: 'error',
                    background: '#ffffff',
                    confirmButtonColor: '#1e293b',
                    confirmButtonText: 'KAPAT',
                    customClass: {
                        popup: '!rounded-[32px] !border-0 !shadow-2xl',
                        title: '!font-black !tracking-tighter !text-red-600',
                        confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                        htmlContainer: '!font-bold !text-slate-500'
                    }
                });
            }
        });
    }

    // ==========================================
    // SHARED CANCEL MODAL
    // ==========================================

    async function handleCancelClick(orderId, platform, trackingId, event) {
        const btn = event ? event.currentTarget : null;
        const originalContent = btn ? btn.innerHTML : '';

        _cancelModalOrderId = orderId;
        _cancelModalTrackingId = trackingId;
        _cancelModalPlatform = platform;

        const platformsWithReasons = ['getir', 'trendyol', 'migros', 'yemeksepeti'];
        const currentPlatform = platform ? platform.toLowerCase() : '';

        const reasonArea = document.getElementById('sharedReasonSelectionArea');
        const cancelReasonTextarea = document.getElementById('sharedCancelReason');
        if (cancelReasonTextarea) cancelReasonTextarea.value = '';

        // Cancel modal yok (admin paneli gibi) — iptal sessizce geç
        const modalEl = document.getElementById('sharedCancelModal');
        if (!modalEl) return;

        // Cancel modal info güncelle
        const cancelInfo = document.getElementById('cancelModalOrderInfo');
        if (cancelInfo) cancelInfo.textContent = `Sipariş #${trackingId}`;

        // Onay butonunu bağla
        const confirmBtn = document.getElementById('sharedCancelConfirmBtn');
        if (confirmBtn) confirmBtn.onclick = function() { confirmCancelShared(); };

        if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
        let myModal = bootstrap.Modal.getInstance(modalEl);
        if (!myModal) myModal = new bootstrap.Modal(modalEl, { backdrop: false });

        if (!platformsWithReasons.includes(currentPlatform)) {
            if (reasonArea) reasonArea.innerHTML = '';
            myModal.show();
            return;
        }

        try {
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Bekleniyor...`;
            }

            const response = await fetch(`/{{$key}}/entegra/reject-statuses/${orderId}`);
            const result = await response.json();

            const reasons = (result.data && result.data.data && result.data.data.data)
                ? result.data.data.data
                : [];

            if (!result.success || reasons.length === 0) {
                Swal.fire({
                    title: 'DURUM KRİTİK',
                    text: 'İptal nedenleri yüklenemedi veya sipariş iptal edilemez durumda.',
                    icon: 'warning',
                    background: '#ffffff',
                    confirmButtonColor: '#1e293b',
                    confirmButtonText: 'ANLADIM',
                    customClass: {
                        popup: '!rounded-[32px] !border-0 !shadow-2xl',
                        title: '!font-black !tracking-tighter !text-slate-800',
                        confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                        htmlContainer: '!font-bold !text-slate-500'
                    }
                });
                return;
            }

            let html = '<label class="form-label mb-2 fw-bold text-muted small">İPTAL NEDENİ SEÇİNİZ</label>';
            html += '<div class="list-group shadow-sm border" style="border-radius: 12px; overflow: hidden;">';

            reasons.forEach((item, index) => {
                html += `
                <div class="list-group-item list-group-item-action border-0 border-bottom">
                    <div class="form-check w-100" style="cursor:pointer;">
                        <input class="form-check-input mt-2" type="radio"
                               name="sharedPlatformReasonId"
                               id="shared_reason_${index}"
                               value="${item.name}"
                               ${index === 0 ? 'checked' : ''}>
                        <label class="form-check-label d-block p-2" style="cursor:pointer;" for="shared_reason_${index}">
                            <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">${item.description}</span>
                        </label>
                    </div>
                </div>`;
            });

            html += '</div>';
            reasonArea.innerHTML = html;

            myModal.show();

        } catch (error) {
            console.error("Hata:", error);
            Swal.fire({
                title: 'BAĞLANTI HATASI',
                text: 'Sunucu ile bağlantı sağlanamadı.',
                icon: 'error',
                background: '#ffffff',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'TEKRAR DENE',
                customClass: {
                    popup: '!rounded-[32px] !border-0 !shadow-2xl',
                    title: '!font-black !tracking-tighter !text-red-600',
                    confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                    htmlContainer: '!font-bold !text-slate-500'
                }
            });
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }
    }

    function confirmCancelShared() {
        const orderId = _cancelModalOrderId;
        const trackingId = _cancelModalTrackingId;
        const platform = _cancelModalPlatform;

        if (!orderId) return;

        const noteArea = document.getElementById('sharedCancelReason');
        const selectedReasonElement = document.querySelector('input[name="sharedPlatformReasonId"]:checked');

        const note = noteArea ? noteArea.value.trim() : '';
        const reasonKey = selectedReasonElement ? selectedReasonElement.value : null;

        const platformsWithReasons = ['getir', 'trendyol', 'migros', 'yemeksepeti'];
        if (platformsWithReasons.includes(platform.toLowerCase()) && !reasonKey) {
            Swal.fire({
                title: 'SEÇİM YAPIN',
                text: 'Lütfen bir iptal nedeni seçiniz.',
                icon: 'warning',
                background: '#ffffff',
                confirmButtonColor: '#1e293b',
                confirmButtonText: 'TAMAM',
                customClass: {
                    popup: '!rounded-[32px] !border-0 !shadow-2xl',
                    title: '!font-black !tracking-tighter !text-slate-800',
                    confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                    htmlContainer: '!font-bold !text-slate-500'
                }
            });
            return;
        }

        const confirmBtn = document.getElementById('sharedCancelConfirmBtn');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> İşleniyor...`;

        sendOrderStatusUpdate('UNSUPPLIED', trackingId, platform, note, orderId, reasonKey)
            .then(() => {
                const modalEl = document.getElementById('sharedCancelModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                setTimeout(cleanupModalBackdrop, 150);

                Swal.fire({
                    title: 'İŞLEM BAŞARILI',
                    text: 'Sipariş iptal edildi.',
                    icon: 'success',
                    background: '#ffffff',
                    confirmButtonColor: '#1e293b',
                    confirmButtonText: 'TAMAM',
                    customClass: {
                        popup: '!rounded-[32px] !border-0 !shadow-2xl',
                        title: '!font-black !tracking-tighter !text-slate-800',
                        confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest'
                    }
                });
            })
            .catch((err) => {
                console.error("İptal Hatası:", err);
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
                Swal.fire({
                    title: 'HATA OLUŞTU',
                    text: 'İptal işlemi başarısız oldu.',
                    icon: 'error',
                    background: '#ffffff',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'TEKRAR DENE',
                    customClass: {
                        popup: '!rounded-[32px] !border-0 !shadow-2xl',
                        title: '!font-black !tracking-tighter !text-red-600',
                        confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest'
                    }
                });
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
            });
    }

    // ==========================================
    // STATUS & ORDER FUNCTIONS
    // ==========================================

    function StatusOrderChange(e, id) {
        var action = e.target.value;
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();
        var selectEl = e.target;

        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1" role="status"></div>
        <small>Bekleniyor...</small>`;
        selectEl.parentNode.appendChild(loadingSpan);

        if (action === 'UNSUPPLIED') {
            handleCancelClick(id, platform, tracking_id, null);
            loadingSpan.remove();
        } else {
            sendOrderStatusUpdate(action, tracking_id, platform, null, null)
                .finally(() => loadingSpan.remove());
        }
    }

    function updateStatusDirectly(id, nextStatus) {
        const tracking_id = $('#tracking_' + id).val();
        const platform = $('#platform_' + id).val();
        const container = document.getElementById(`action-container-${id}`);

        if (container) {
            container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <div class="small">İşleniyor...</div>
            </div>`;
        }

        sendOrderStatusUpdate(nextStatus, tracking_id, platform, null, null);
    }

    async function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId, entegraReasonId) {
        return new Promise((resolve, reject) => {
            var requestData = {
                action: action,
                tracking_id: tracking_id,
                _token: '{{ csrf_token() }}'
            };

            if (message) requestData.message = message;
            if (entegraReasonId) requestData.entegraReasonId = entegraReasonId;

            $.ajax({
                type: 'POST',
                url: '/{{$key}}/' + platform + '/updateOrderStatus',
                data: requestData,
                success: function (data) {
                    Swal.fire({
                        title: 'GÜNCELLENDİ',
                        text: 'Sipariş durumu başarıyla güncellendi.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        background: '#ffffff',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800',
                            htmlContainer: '!font-bold !text-slate-500'
                        }
                    });

                    // Modal temizliği
                    cleanupModalBackdrop();

                    if (typeof refreshOrderTable === "function") {
                        refreshOrderTable(data.order);
                    }

                    resolve(data);
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'HATA OLUŞTU',
                        text: xhr.responseText || 'Sunucu tarafında beklenmedik bir hata meydana geldi.',
                        icon: 'error',
                        background: '#ffffff',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'TAMAM',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-red-600',
                            confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                            htmlContainer: '!font-bold !text-slate-500 !text-sm'
                        }
                    });
                    reject(error);
                }
            });
        });
    }

    // ==========================================
    // TABLE REFRESH
    // ==========================================

    async function refreshOrderTable(order, autoSwitchTab) {
        let targetStatusKey = order.status;

        if (order.status === 'PREPARED' && order.courier_id && order.courier_id != -1) {
            targetStatusKey = 'ASSIGNED';
        }

        const tabId = statusMap[targetStatusKey];
        const rowId = 'data_' + order.id;

        const newRowHtml = generateOrderRowHtml(order);
        const existingRow = $('#' + rowId);

        if (existingRow.length) {
            const parentTable = existingRow.closest("table");
            const currentTabId = parentTable.attr("id");

            if (currentTabId !== tabId) {
                existingRow.remove();
                $('#order-tbody-' + tabId).append(newRowHtml);
                // Sipariş başka taba geçti — o tabı göster
                $(`#${tabId}-tab`).tab('show');
            } else {
                existingRow.replaceWith(newRowHtml);
            }
        } else {
            const targetBody = $('#order-tbody-' + tabId);
            if (targetBody.length) {
                targetBody.append(newRowHtml);
            } else {
                $(`#${tabId}`).find('tbody').append(newRowHtml);
            }
            // Yeni sipariş — ilgili tabı göster (autoSwitchTab=true ise)
            if (autoSwitchTab) {
                $(`#${tabId}-tab`).tab('show');
            }
        }

        Object.keys(statusMap).forEach(status => updateTableForStatus(status));
        if (targetStatusKey === 'ASSIGNED') updateTableForStatus('ASSIGNED');
    }

    function updateTableForStatus(status) {
        const tabId = statusMap[status];
        if (!tabId) return;

        const tableBody = document.querySelector(`#${tabId} tbody`);
        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr:not(.no-order-row)');

            if (rows.length === 0) {
                tableBody.innerHTML = `
                <tr class="no-order-row">
                    <td colspan="12" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-box-open mb-2 text-muted" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-muted">Bu kategoride henüz sipariş bulunmuyor.</span>
                        </div>
                    </td>
                </tr>`;
            } else {
                const noOrderRow = tableBody.querySelector('.no-order-row');
                if (noOrderRow) noOrderRow.remove();
            }
        }
    }

    // ==========================================
    // ORDER MODAL & ACTIONS
    // ==========================================

    function openOrderModal(order) {
        const container = document.getElementById('OrdersModal');
        const modalBody = document.querySelector("#OrdersModal .modal-body");

        // Subtitle güncelle
        const subtitle = document.getElementById('orderModalSubtitle');
        if (subtitle) subtitle.textContent = `#${order.tracking_id} — ${order.platform || ''}`;

        modalBody.innerHTML = `
        <div class="row">
            <div class="mb-3 col-md-6">
                <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Sipariş Kodu</p>
                <p style="font-size: 13px; font-weight: 800; color: #4f46e5; margin: 0;">#${order.tracking_id}</p>
            </div>
            <div class="mb-3 col-md-6">
                <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Müşteri Adı</p>
                <p style="font-size: 13px; font-weight: 800; color: #1e293b; text-transform: uppercase; margin: 0;">${order.full_name}</p>
            </div>
            <div class="mb-3 col-md-4">
                <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Telefon</p>
                <p style="font-size: 13px; font-weight: 800; color: #1e293b; margin: 0;">${order.phone}</p>
            </div>
            <div class="mb-3 col-md-4">
                <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Tutar</p>
                <p style="font-size: 13px; font-weight: 800; color: #1e293b; margin: 0;">${order.amount} ₺</p>
            </div>
            <div class="mb-3 col-md-4">
                <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">Ödeme Yön.</p>
                <p style="font-size: 13px; font-weight: 800; color: #64748b; font-style: italic; margin: 0;">${order.payment_method}</p>
            </div>
            <div class="mb-3 col-md-12">
                <p style="font-size: 10px; font-weight: 900; color: #4f46e5; text-transform: uppercase; margin-bottom: 2px;">Adres</p>
                <p style="font-size: 12px; font-weight: 700; color: #334155; line-height: 1.4; margin: 0;">${order.address}</p>
            </div>
            <div class="mb-3 col-md-12">
                <p style="font-size: 10px; font-weight: 900; color: #6366f1; text-transform: uppercase; margin-bottom: 2px;">Müşteri Notu</p>
                <p style="font-size: 11px; font-weight: 700; color: #475569; font-style: italic; margin: 0; background: #f8fafc; padding: 10px; border-radius: 8px; border-left: 3px solid #6366f1;">
                    ${order.notes ?? 'Bulunmuyor.'}
                </p>
            </div>
        </div>`;

        const items = JSON.parse(order.items);
        let tableHTML = `
        <div class="mb-3 mt-4 col-md-12">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="font-size: 11px; font-weight: 900; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;">Ürün</th>
                        <th style="font-size: 11px; font-weight: 900; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;" class="text-center">Adeti</th>
                        <th style="font-size: 11px; font-weight: 900; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0;" class="text-end">Fiyatı</th>
                    </tr>
                </thead>
                <tbody>`;

        items.forEach(item => {
            tableHTML += `
            <tr>
                <td style="font-size: 12px; font-weight: 800; color: #1e293b; text-transform: uppercase;">${item.name}</td>
                <td style="font-size: 12px; font-weight: 800; color: #64748b;" class="text-center">x${item.quantity}</td>
                <td style="font-size: 12px; font-weight: 800; color: #1e293b;" class="text-end">${item.price} ₺</td>
            </tr>`;
        });

        tableHTML += `</tbody></table></div>`;
        modalBody.innerHTML += tableHTML;

        let modal = bootstrap.Modal.getInstance(container);
        if (!modal) modal = new bootstrap.Modal(container, { backdrop: false });
        modal.show();

        document.getElementById("printOrderBtn").onclick = function () {
            printOrder(order);
        };
    }

    function printOrder(order) {
        // Backend'e yazdırıldı bilgisini gönder
        fetch('/{{$key}}/printed/' + order.id).catch(err => console.error(err));

        // Sipariş bilgilerini çıktı al
        const items = typeof order.items === 'string' ? JSON.parse(order.items) : (order.items || []);
        const amount = order.amount ? parseFloat(order.amount).toFixed(2) : '0.00';
        const discount = order.discount ? parseFloat(order.discount).toFixed(2) : '0.00';
        const subAmount = order.sub_amount ? parseFloat(order.sub_amount).toFixed(2) : '0.00';
        const createdAt = order.created_at ? new Date(order.created_at).toLocaleString('tr-TR', {
            day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
        }) : '';

        let itemsRows = '';
        items.forEach(item => {
            itemsRows += `
                <tr>
                    <td style="padding:8px 12px; font-size:12px; font-weight:700; border-bottom:1px solid #e2e8f0;">${item.name}</td>
                    <td style="padding:8px 12px; font-size:12px; font-weight:700; text-align:center; border-bottom:1px solid #e2e8f0;">x${item.quantity}</td>
                    <td style="padding:8px 12px; font-size:12px; font-weight:700; text-align:right; border-bottom:1px solid #e2e8f0;">${item.price} ₺</td>
                </tr>`;
        });

        const printContent = `
        <html>
        <head>
            <title>Sipariş #${order.tracking_id}</title>
            <style>
                * { margin:0; padding:0; box-sizing:border-box; }
                body { font-family: 'Segoe UI', Tahoma, sans-serif; padding:30px; color:#1e293b; }
                .header { text-align:center; border-bottom:2px solid #4f46e5; padding-bottom:16px; margin-bottom:20px; }
                .header h2 { font-size:18px; font-weight:900; letter-spacing:-0.5px; }
                .header p { font-size:11px; color:#64748b; margin-top:4px; }
                .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
                .info-box { background:#f8fafc; padding:10px 14px; border-radius:8px; }
                .info-box .label { font-size:9px; font-weight:900; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; }
                .info-box .value { font-size:13px; font-weight:800; margin-top:2px; }
                .full-width { grid-column: 1 / -1; }
                table { width:100%; border-collapse:collapse; margin-top:16px; }
                thead th { font-size:10px; font-weight:900; color:#64748b; text-transform:uppercase; padding:8px 12px; border-bottom:2px solid #e2e8f0; text-align:left; }
                thead th:nth-child(2) { text-align:center; }
                thead th:nth-child(3) { text-align:right; }
                .totals { margin-top:16px; text-align:right; border-top:2px solid #1e293b; padding-top:12px; }
                .totals .line { font-size:12px; margin-bottom:4px; }
                .totals .total { font-size:16px; font-weight:900; }
                .footer { text-align:center; margin-top:30px; font-size:10px; color:#94a3b8; }
                @media print { body { padding:15px; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>SİPARİŞ FİŞİ</h2>
                <p>#${order.tracking_id} &nbsp;|&nbsp; ${order.platform_date ?? createdAt}</p>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="label">Müşteri</div>
                    <div class="value">${order.full_name || '-'}</div>
                </div>
                <div class="info-box">
                    <div class="label">Telefon</div>
                    <div class="value">${order.phone || '-'}</div>
                </div>
                <div class="info-box">
                    <div class="label">Ödeme</div>
                    <div class="value">${order.payment_method || '-'}</div>
                </div>
                <div class="info-box">
                    <div class="label">Platform</div>
                    <div class="value">${order.platform || '-'}</div>
                </div>
                <div class="info-box full-width">
                    <div class="label">Adres</div>
                    <div class="value" style="font-size:12px; line-height:1.4;">${order.address || '-'}</div>
                </div>
                ${order.notes ? `<div class="info-box full-width">
                    <div class="label">Müşteri Notu</div>
                    <div class="value" style="font-size:12px; font-style:italic; color:#475569;">${order.notes}</div>
                </div>` : ''}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Adet</th>
                        <th>Fiyat</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsRows}
                </tbody>
            </table>

            <div class="totals">
                <div class="line">Ara Toplam: <strong>${subAmount} ₺</strong></div>
                ${parseFloat(discount) > 0 ? `<div class="line" style="color:#dc2626;">İndirim: <strong>-${discount} ₺</strong></div>` : ''}
                <div class="total">TOPLAM: ${amount} ₺</div>
            </div>

            <div class="footer">Bu fiş otomatik olarak oluşturulmuştur.</div>
        </body>
        </html>`;

        const win = window.open('', '_blank', 'width=400,height=700');
        win.document.write(printContent);
        win.document.close();
        setTimeout(() => {
            win.print();
            win.close();
        }, 400);
    }

    function deleteOrder(order) {
        let orderid = order;

        $.ajax({
            type: 'GET',
            url: '/{{$key}}/orders/delete/' + orderid,
            success: function (data) {
                if (data == "OK") {
                    $('#data_' + orderid).fadeOut(300, function() { $(this).remove(); });
                    Swal.fire({
                        title: 'SİLİNDİ',
                        text: 'Sipariş başarıyla sistemden kaldırıldı.',
                        icon: 'success',
                        background: '#ffffff',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'TAMAM',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800',
                            confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                            htmlContainer: '!font-bold !text-slate-500'
                        }
                    });
                }
                if (data == "ERR") {
                    Swal.fire({
                        title: 'SİPARİŞ SİLİNEMEDİ',
                        text: 'Sipariş sistemden kaldırılamadı.',
                        icon: 'error',
                        background: '#ffffff',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'TAMAM',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800',
                            confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest',
                            htmlContainer: '!font-bold !text-slate-500'
                        }
                    });
                }
            },
            error: function () {
                console.error('Sipariş silinirken hata oluştu');
            }
        });
    }

    // ==========================================
    // HELPERS
    // ==========================================

    function fetchCouriers() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '/{{$key}}/get-couriers',
                success: function (data) {
                    resolve(data);
                },
                error: function (xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function formatDistance(distanceKm) {
        const km = parseFloat(distanceKm);
        if (isNaN(km)) return 'Geçersiz mesafe';

        if (km >= 1) {
            return `${km.toFixed(2)} km`;
        } else if (km >= 0.001) {
            return `${(km * 1000).toFixed(2)} m`;
        } else {
            return `${(km * 100000).toFixed(2)} cm`;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ==========================================
    // ROW GENERATION (NO INLINE MODALS)
    // ==========================================

    function generateOrderRowHtml(order) {
        const restaurantName = order.restaurant ? order.restaurant.restaurant_name : 'İsim Yok';
        const trackingId = order.tracking_id || '';
        const fullName = order.full_name || '';
        const total = (order.sub_amount !== null && order.sub_amount !== undefined) ? parseFloat(order.sub_amount).toFixed(2) : '0.00';
        const amount = (order.amount !== null && order.amount !== undefined) ? parseFloat(order.amount).toFixed(2) : '0.00';
        const discount = (order.discount !== null && order.discount !== undefined) ? parseFloat(order.discount).toFixed(2) : '0.00';
        const platform = order.platform || '';
        const createdAt = order.created_at ? new Date(order.created_at).toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }) : '';

        const status = order.status;
        const distanceStr = order.distance ? formatDistance(order.distance) : '';

        let platformHtml = '';
        const basePlatformClass = "inline-flex items-center gap-2 bg-white border border-slate-100 px-3 py-1.5 rounded-2xl shadow-sm transition-all hover:shadow-md";

        if (platform.toLowerCase() === 'yemeksepeti') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height:14px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${escapeHtml(restaurantName)}</span></div>`;
        } else if (platform.toLowerCase() === 'getir') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/platforms/getir.png') }}" style="height:28px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${escapeHtml(restaurantName)}</span></div>`;
        } else if (platform.toLowerCase() === 'trendyol') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/platforms/trendyol.png') }}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${escapeHtml(restaurantName)}</span></div>`;
        } else if (platform.toLowerCase() === 'migros') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{asset('theme/images/platforms/migros.png')}}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${escapeHtml(restaurantName)}</span></div>`;
        } else if (platform.toLowerCase() === 'adisyo') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/adisyoFull.png') }}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${escapeHtml(restaurantName)}</span></div>`;
        } else if (platform.toLowerCase() === 'telefonsiparis') {
            platformHtml = `<div class="inline-flex items-center justify-center bg-slate-900 text-white border border-slate-800 px-4 py-2 rounded-2xl shadow-sm w-full"><span class="text-[10px] font-black tracking-widest uppercase">${escapeHtml(restaurantName)} / POS</span></div>`;
        } else {
            platformHtml = `<span class="bg-slate-100 text-slate-600 text-[10px] font-black px-3 py-2 rounded-xl uppercase tracking-widest">${escapeHtml(restaurantName)}</span>`;
        }

        // Kurye section - NO inline modal, onclick opens shared modal
        let courierSection = '';
        let courierStatusBadge = '';
        const hasCourier = !!(order.courier_id && order.courier_id != -1 && order.courier_id != 0);

        if (status === 'UNSUPPLIED' || status === 'DELIVERED' || status === 'HANDOVER' ) {
            courierSection = `
            <a style="cursor:pointer;" class="flex items-center gap-2 text-brand font-black text-xs no-underline hover:opacity-80">
                <div class="w-8 h-8 bg-brand/10 rounded-xl flex items-center justify-center"><i class="fas fa-truck text-[10px]"></i></div>
                ${order.courier ? escapeHtml(order.courier.name.substr(0, 10)) : 'Kurye Yok'}
            </a>`;
        } else {
            if (order.courier && order.courier.id) {
                if (status === 'ASSIGNED') {
                    courierStatusBadge = '<span class="inline-block  mx-auto text-center mt-2 px-2 py-0.5 bg-green-500 text-white text-[9px] font-black rounded-lg uppercase tracking-tighter"><i class="fas fa-check-double text-[8px] mr-1"></i> Paket Kabul Edildi</span>';
                } else if (status === 'PREPARED') {
                    courierStatusBadge = '<span class="bg-red-500 mx-auto text-center mt-2 inline-block  px-2 py-0.5 bg-brand text-white text-[9px] font-black rounded-lg uppercase tracking-tighter"><i class="fas fa-clock text-[8px] mr-1"></i> Teslimat Bekliyor</span>';
                }

                courierSection = `
                <div class="flex flex-col items-start group">
                    <a onclick="openCourierModal('${order.id}', '${escapeHtml(trackingId)}', true, '${order.restaurant?.latitude || ''}', '${order.restaurant?.longitude || ''}')" style="cursor:pointer;" class="flex items-center gap-2 text-brand font-black text-xs no-underline group-hover:scale-105 transition-transform">
                       <div class="w-8 h-8 bg-brand rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand/20"><i class="fas fa-truck text-[10px]"></i></div>
                       ${escapeHtml(order.courier.name.substr(0, 15))}
                    </a>
                    ${courierStatusBadge}
                </div>`;
            } else {
                @if($key === 'admin')
                courierSection = `
                <button onclick="openCourierModal('${order.id}', '${escapeHtml(trackingId)}', false, '${order.restaurant?.latitude || ''}', '${order.restaurant?.longitude || ''}')" class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-dashed border-slate-200 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-brand hover:text-brand transition-all">
                    <i class="fas fa-plus-circle"></i> KURYE ATA
                </button>`;
                @else
                courierSection = `
                <span class="flex items-center gap-2 px-4 py-2 bg-slate-50 border-2 border-dashed border-slate-100 text-slate-300 rounded-2xl font-black text-[10px] uppercase tracking-widest cursor-not-allowed" title="Kurye atama sadece admin tarafından yapılabilir">
                    <i class="fas fa-lock text-[9px]"></i> KURYE ATA
                </span>`;
                @endif
            }
        }

        // Escape order data for onclick
        const orderJsonSafe = JSON.stringify(order).replace(/'/g, "\\'").replace(/"/g, '&quot;');

        @if($key === 'admin')
        const rowCheckbox = (status === 'PREPARED' || status === 'PENDING')
            ? `<td class="py-4 px-3"><input type="checkbox" class="bulk-order-checkbox rounded" value="${order.id}" onchange="updateBulkBar()"></td>`
            : `<td class="py-4 px-3"></td>`;
        @else
        const rowCheckbox = '';
        @endif

        return `
        <tr id="data_${order.id}" class="hover:bg-slate-50/50 transition-colors border-b border-slate-50">
            ${rowCheckbox}
            <td class="py-2 px-3">${platformHtml}<input type="hidden" value="${escapeHtml(trackingId)}" id="tracking_${order.id}"></td>
            <td class="py-2 px-3"><span class="font-black text-slate-400 text-xs tracking-widest">#${escapeHtml(trackingId)}</span></td>
            <td class="py-2 px-3 text-[11px] font-bold text-slate-500 italic">${order.platform_date ?? createdAt}</td>
            <td class="py-2 px-3"><span class="font-black text-slate-800 text-xs uppercase tracking-tighter truncate block" style="max-width:140px;">${escapeHtml(fullName)}</span></td>
            <td class="py-2 px-3">${courierSection}</td>
            <td class="py-2 px-3 font-black text-slate-800 text-xs">${total} ₺</td>
            <td class="py-2 px-3 font-bold text-red-400 text-[10px] italic">-${discount} ₺</td>
            <td class="py-2 px-3 font-black text-slate-900 text-xs">${amount} ₺</td>
            <td class="py-2 px-3"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">${order.payment_method}</span></td>
            <td class="py-2 px-3"><strong class="text-slate-900 font-black italic text-[10px]" id="distance${order.id}">${distanceStr}</strong></td>
            <td class="py-2 px-3">
                <input type="hidden" id="tracking_${order.id}" value="${escapeHtml(trackingId)}">
                <input type="hidden" id="platform_${order.id}" value="${escapeHtml(platform)}">

                <div class="flex flex-col gap-1" style="min-width:90px;" id="action-container-${order.id}">
                    ${status === 'PENDING' ? `<button class="w-full py-2 bg-brand text-white rounded-xl font-black text-[10px] uppercase tracking-tighter shadow-lg shadow-brand/20 hover:scale-105 transition-transform border-0" onclick="updateStatusDirectly('${order.id}', 'PREPARED')">Hazırlandı</button>` : ''}
                    ${status === 'HANDOVER' ? `<button class="w-full py-2 bg-green-500 text-white rounded-xl font-black text-[10px] uppercase tracking-tighter shadow-lg shadow-green-500/20 hover:scale-105 transition-transform border-0" onclick="updateStatusDirectly('${order.id}', 'DELIVERED')">Teslim Edildi</button>` : ''}
                    ${status !== 'DELIVERED' && status !== 'UNSUPPLIED'
                        ? `<button class="w-full py-2 border border-red-100 text-red-500 rounded-xl font-black text-[10px] uppercase tracking-tighter hover:bg-red-50 transition-colors bg-white" onclick="handleCancelClick('${order.id}', '${escapeHtml(platform)}', '${escapeHtml(trackingId)}', event)">İptal Et</button>`
                        : `<span class="bg-slate-50 text-slate-300 text-center py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border border-slate-100">${status === 'DELIVERED' ? 'Tamamlandı' : 'İptal Edildi'}</span>`
                    }
                </div>
            </td>
            <td class="py-2 px-3">
                <div class="flex items-center gap-1.5">
                    <button class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand hover:border-brand shadow-sm transition-all active:scale-90"
                       onclick='openOrderModal(${JSON.stringify(order)})'>
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                    <button onclick='printOrder(${JSON.stringify(order)})' class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-500 shadow-sm transition-all active:scale-90 cursor-pointer">
                        <i class="fas fa-print text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }

    @if($key === 'admin')
    // ═══════════════════════════════════════
    // TOPLU ATAMA
    // ═══════════════════════════════════════
    function updateBulkBar() {
        const checked = document.querySelectorAll('.bulk-order-checkbox:checked');
        const bar = document.getElementById('bulkAssignBar');
        const countEl = document.getElementById('bulkSelectedCount');
        if (!bar) return;
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            countEl.textContent = checked.length;
        } else {
            bar.classList.add('hidden');
        }
    }

    function toggleSelectAll(masterCb, statusId) {
        const tbody = document.getElementById('order-tbody-' + statusId);
        if (!tbody) return;
        tbody.querySelectorAll('.bulk-order-checkbox').forEach(cb => cb.checked = masterCb.checked);
        updateBulkBar();
    }

    function clearBulkSelection() {
        document.querySelectorAll('.bulk-order-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.select-all-checkbox').forEach(cb => cb.checked = false);
        updateBulkBar();
    }

    // Kuryeler yükle ve select'e doldur
    async function loadBulkCourierSelect() {
        const sel = document.getElementById('bulkCourierSelect');
        if (!sel || sel.options.length > 1) return;
        try {
            const couriers = await fetchCouriers();
            couriers.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name + (c.status === 'active' ? ' (Müsait)' : ' (Yolda)');
                sel.appendChild(opt);
            });
        } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadBulkCourierSelect();
        // Bar görününce kurye listesini tekrar yükle
        const bar = document.getElementById('bulkAssignBar');
        if (bar) new MutationObserver(() => { if (!bar.classList.contains('hidden')) loadBulkCourierSelect(); }).observe(bar, { attributes: true });
    });

    async function bulkAssignOrders() {
        const checked = [...document.querySelectorAll('.bulk-order-checkbox:checked')];
        const courierId = document.getElementById('bulkCourierSelect')?.value;
        if (!checked.length || !courierId) {
            alert('Sipariş ve kurye seçin.');
            return;
        }
        const orderIds = checked.map(cb => cb.value);
        const btn = document.getElementById('bulkAssignBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const res = await fetch('/admin/orders/bulk-assign', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                body: JSON.stringify({ order_ids: orderIds, courier_id: courierId }),
            });
            const data = await res.json();
            if (data.success) {
                clearBulkSelection();
                fetchOrders();
            } else {
                alert(data.message || 'Hata oluştu.');
            }
        } catch(e) {
            alert('İstek gönderilemedi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Ata';
        }
    }
    @endif
</script>
