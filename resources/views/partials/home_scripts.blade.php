<script>
    const statusMap = {!! json_encode(\App\Helpers\OrderStatus::statuses()) !!};

    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();
    });

    function fetchOrders(status) {
        $.ajax({
            url: '/{{$key}}/orders/ajax',
            method: 'GET', // veya 'POST' gerekiyorsa
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
    Pusher.logToConsole = false; // debug için true yapabilirsiniz
    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    let keyId = "{{ auth($key)->id() }}";

    var channel = pusher.subscribe(`{{$key}}-${keyId}`);

    // Gelen veriyi konsolda görebilmek için
    channel.bind('new-order', function (data) {
        console.log('Gelen data:', data);
        if (data.order) {
            refreshOrderTable(data.order);

            if ('{{\App\Helpers\OrdersHelper::getOrderSystem(1)}}') {
                const audio = new Audio('{{asset('voices/order/beep-warning-6387.mp3')}}');
                audio.play().catch(err => {
                    console.error("Ses çalma başarısız:", err);
                });

                const newOrderEl = document.getElementById('newOrder');
                newOrderEl.style.display = 'block'; // Görünür yap
                newOrderEl.classList.add('blink'); // Yanıp sönme efekti

                // 3 saniye sonra gizle
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
            refreshOrderTable(data.order);
        }
    });

    function StatusOrderChange(e, id) {
        var action = e.target.value;
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();
        var selectEl = e.target;

        // Spinner ekle
        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1" role="status"></div>
        <small>Bekleniyor...</small>`;
        selectEl.parentNode.appendChild(loadingSpan);

        if (action === 'UNSUPPLIED') {
            // O siparişe özel modalı aç
            var myModal = new bootstrap.Modal(document.getElementById('cancelModal' + id));
            myModal.show();

            // Spinner'ı modal açıldığı için temizle (işlem modal içinde devam edecek)
            loadingSpan.remove();

            // Select box'ı eski haline getirmek isteyebilirsiniz (isteğe bağlı)
        } else {
            // Diğer durumlar için doğrudan güncelle
            sendOrderStatusUpdate(action, tracking_id, platform, null, null)
                .finally(() => loadingSpan.remove());
        }
    }

    async function handleCancelClick(orderId, platform, trackingId, event) {
        const btn = event ? event.currentTarget : null;
        const originalContent = btn ? btn.innerHTML : '';

        const platformsWithReasons = ['getir', 'trendyol', 'migros', 'yemeksepeti'];
        const currentPlatform = platform ? platform.toLowerCase() : '';

        const modalElement = document.getElementById('cancelModal' + orderId);
        const reasonArea = document.getElementById('reasonSelectionArea' + orderId);

        let myModal = bootstrap.Modal.getInstance(modalElement);
        if (!myModal) myModal = new bootstrap.Modal(modalElement);

        if (!platformsWithReasons.includes(currentPlatform)) {
            reasonArea.innerHTML = '';
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

            // DERİN VERİ KONTROLÜ: result.data.data.data (JSON yapına göre)
            const reasons = (result.data && result.data.data && result.data.data.data)
                ? result.data.data.data
                : [];

            // Başarısızlık veya Boş Liste Kontrolü
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

            // HTML Oluşturma (Card/List Group Tasarımı)
            let html = '<label class="form-label mb-2 fw-bold text-muted small">İPTAL NEDENİ SEÇİNİZ</label>';
            html += '<div class="list-group shadow-sm border rounded">';

            reasons.forEach((item, index) => {
                html += `
            <div class="list-group-item list-group-item-action border-0 border-bottom">
                <div class="form-check w-100 cursor-pointer">
                    <input class="form-check-input mt-2" type="radio"
                           name="platformReasonId${orderId}"
                           id="reason_${orderId}_${index}"
                           value="${item.name}"
                           ${index === 0 ? 'checked' : ''}>
                    <label class="form-check-label d-block p-2 cursor-pointer stretched-link" for="reason_${orderId}_${index}">
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

    function confirmCancel(orderId, trackingId, platform) {
        // 1. Elementleri ve Değerleri Al
        const noteArea = document.getElementById('cancelReason' + orderId);
        const selectedReasonElement = document.querySelector(`input[name="platformReasonId${orderId}"]:checked`);

        // Değerleri oku
        const note = noteArea ? noteArea.value.trim() : '';
        const reasonKey = selectedReasonElement ? selectedReasonElement.value : null;

        // Eğer platform neden gerektiriyorsa ve seçilmemişse durdur (Opsiyonel Güvenlik)
        const platformsWithReasons = ['getir', 'trendyol', 'migros', 'yemeksepeti'];
        if (platformsWithReasons.includes(platform.toLowerCase()) && !reasonKey) {
            Swal.fire({
                title: 'SEÇİM YAPIN',
                text: 'Lütfen bir iptal nedeni seçiniz.',
                icon: 'warning',
                background: '#ffffff',
                confirmButtonColor: '#1e293b', // Dark Slate
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

        // 2. Buton Kilitleme ve Yükleme Simgesi
        const confirmBtn = document.querySelector(`#cancelModal${orderId} .btn-danger`);
        if (!confirmBtn) return;

        const originalText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> İşleniyor...`;

        // 3. API İsteği
        // Parametrelerin sırasını sendOrderStatusUpdate fonksiyonunun tanımına göre kontrol etmelisin
        sendOrderStatusUpdate('UNSUPPLIED', trackingId, platform, note, orderId, reasonKey)
            .then(() => {
                // Başarılı ise modalı kapat (Bootstrap 5)
                const modalEl = document.getElementById('cancelModal' + orderId);
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                Swal.fire({
                    title: 'İŞLEM BAŞARILI',
                    text: 'Sipariş iptal edildi.',
                    icon: 'success',
                    background: '#ffffff',
                    confirmButtonColor: '#1e293b', // Dark Slate
                    confirmButtonText: 'TAMAM',
                    buttonsStyling: true,
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
                    confirmButtonColor: '#ef4444', // Red
                    confirmButtonText: 'TEKRAR DENE',
                    customClass: {
                        popup: '!rounded-[32px] !border-0 !shadow-2xl',
                        title: '!font-black !tracking-tighter !text-red-600',
                        confirmButton: '!rounded-2xl !px-5 !py-3 !text-[10px] !font-black !tracking-widest'
                    }
                });
            });
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

    function cancelOrder(id) {
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();
        var cancelReason = $('#cancelReason' + id).val();
        var action = 'UNSUPPLIED';

        if (!cancelReason || cancelReason.trim() === '') {
            Swal.fire({
                title: 'BİLGİ EKSİK',
                text: 'Lütfen iptal nedenini belirtin.',
                icon: 'warning',
                background: '#ffffff',
                confirmButtonColor: '#f59e0b', // Amber/Warning Rengi
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

        // Butonu pasif yapalım ki mükerrer tıklanmasın
        const btn = event.target;
        btn.disabled = true;

        sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, null)
            .then(() => {
                // Modalı kapat
                const modalEl = document.getElementById('cancelModal' + id);
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
            })
            .finally(() => {
                btn.disabled = false;
            });
    }

    async function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId, entegraReasonId) {
        // Promise döndürüyoruz ki updateStatusDirectly içindeki .then() çalışsın
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
                        timerProgressBar: true, // Alt kısımda ince bir ilerleme çubuğu
                        background: '#ffffff',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800',
                            htmlContainer: '!font-bold !text-slate-500'
                        }
                    });

                    // --- MODAL VE GRİ EKRAN TEMİZLİĞİ ---
                    // Dinamik ID ile modalı bul ve kapat
                    const modalEl = document.getElementById('cancelModal' + orderId);
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    // Manuel Backdrop Temizliği (Garanti yöntem)
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                    // ------------------------------------

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

    async function refreshOrderTable(order) {
        // ÖNEMLİ: Mantıksal Tab Belirleme
        let targetStatusKey = order.status;

        // Eğer statü PREPARED ama kurye atanmışsa, onu ASSIGNED sekmesine zorla
        if (order.status === 'PREPARED' && order.courier_id && order.courier_id != -1) {
            targetStatusKey = 'ASSIGNED';
        }

        const tabId = statusMap[targetStatusKey]; // Hedef tablo ID'si
        const rowId = 'data_' + order.id;

        const newRowHtml = await generateOrderRowHtml(order);
        const existingRow = $('#' + rowId);

        if (existingRow.length) {
            const parentTable = existingRow.closest("table");
            const currentTabId = parentTable.attr("id"); // Mevcut olduğu tablo ID'si

            // Eğer olması gereken yer ile olduğu yer farklıysa taşı
            if (currentTabId !== tabId) {
                existingRow.remove();
                $('#order-tbody-' + tabId).append(newRowHtml); // Tbody ID'nize göre güncelleyin
            } else {
                existingRow.replaceWith(newRowHtml);
            }
        } else {
            // İlk kez ekleniyorsa
            const targetBody = $('#order-tbody-' + tabId);
            if(targetBody.length) {
                targetBody.append(newRowHtml);
            } else {
                // Eğer tbody ID formatınız farklıysa (tabId doğrudan id ise):
                $(`#${tabId}`).find('tbody').append(newRowHtml);
            }
        }

        // Tablo boş/dolu uyarısını güncelle
        Object.keys(statusMap).forEach(status => updateTableForStatus(status));
        if (targetStatusKey === 'ASSIGNED') updateTableForStatus('ASSIGNED');

        if (order.status === 'HANDOVER') {
            await updateCourierOptions(order.id);
        }
    }

    async function updateCourierOptions(orderId) {
        try {
            const couriers = await fetchCouriers();
            const selectElement = document.querySelector(`#Courier${orderId} select`);

            if (selectElement) {
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }

                couriers.forEach(courier => {
                    const option = document.createElement('option');
                    option.value = courier.id;
                    option.textContent = courier.name;
                    selectElement.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Kurye listesi güncellenirken hata:', error);
        }
    }

    function updateTables(data) {
        // Temizle
        Object.keys(data).forEach(status => {
            const tabId = statusMap[status];
            const tbody = document.querySelector(`#${tabId} tbody`);
            if (tbody) {
                tbody.innerHTML = '';
                data[status].forEach(order => {
                    // Her sipariş için satır ekle
                    const row = document.createElement('tr');
                    // Satıra sütunlar ekle
                    // örnek:
                    row.innerHTML = `<td>${order.id}</td><td>${order.customer_name}</td>`;
                    tbody.appendChild(row);
                });
            }
        });
    }

    function openOrderModal(order) {
        const container = document.getElementById('OrdersModal');
        const modalBody = document.querySelector("#OrdersModal .modal-body");

        // Mevcut yapını koruyarak Indigo renklerini ve fontlarını gömüyoruz
        modalBody.innerHTML = `
    <div class="row">
        <div class="mb-3 col-md-6">
            <p style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 2px; tracking-widest">Sipariş Kodu</p>
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
    </div>
    `;

        // Ürün tablosu
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
            <tbody>
    `;

        items.forEach(item => {
            tableHTML += `
        <tr>
            <td style="font-size: 12px; font-weight: 800; color: #1e293b; text-transform: uppercase;">${item.name}</td>
            <td style="font-size: 12px; font-weight: 800; color: #64748b;" class="text-center">x${item.quantity}</td>
            <td style="font-size: 12px; font-weight: 800; color: #1e293b;" class="text-end">${item.price} ₺</td>
        </tr>
        `;
        });

        tableHTML += `</tbody></table></div>`;
        modalBody.innerHTML += tableHTML;

        // Senin orijinal modal açma kodun
        let modal = new bootstrap.Modal(container);
        modal.show();

        // Butona yazdır işlevi (senin orijinal kodun)
        document.getElementById("printOrderBtn").onclick = function () {
            let printContent = modalBody.innerHTML;
            let win = window.open("", "_blank", "width=800,height=600");
            win.document.write(`
        <html>
            <head>
                <title>Sipariş Yazdır</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                <style>
                    body { padding: 20px; font-family: sans-serif; }
                    .text-end { text-align: right; }
                    .text-center { text-align: center; }
                </style>
            </head>
            <body>
                <h3 class="fw-bold text-center mb-4 pb-2 border-bottom">SİPARİŞ BİLGİLERİ</h3>
                ${printContent}
            </body>
        </html>
        `);
            win.document.close();
            // Yazıların yüklenmesi için küçük bir delay
            setTimeout(() => {
                win.print();
                win.close();
            }, 300);
        };
    }

    function updateTableForStatus(status) {
        const tabId = statusMap[status];
        if (!tabId) return;

        const tableBody = document.querySelector(`#${tabId} tbody`);
        if (tableBody) {
            // Eğer tabloda hiç <tr> yoksa veya sadece bizim "Sipariş bulunmuyor" yazımız varsa
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
                // Eğer sipariş geldiyse uyarı satırını kaldır
                const noOrderRow = tableBody.querySelector('.no-order-row');
                if (noOrderRow) noOrderRow.remove();
            }
        }
    }

    function printOrder(orderId) {
        fetch('/{{$key}}/printed/' + orderId)
            .then(response => () => {
                console.log({printOrder: response})
                toastr.success("Yeni Sipariş Eklendi ", "Sipariş Başarıyla Eklendi", {
                    positionClass: "toast-top-right",
                    closeButton: true,
                    progressBar: true,
                    timeOut: 1500
                });
            })
            .catch(err => console.error(err));
    }

    function deleteOrder(order) {
        let orderid = order;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/{{$key}}/orders/delete/' + orderid,
            success: function (data) {
                if (data == "OK") {
                    $('#Courier' + orderid).hide();
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
                        text: 'Sipariş  sistemden kaldırılamadı.',
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

            },
            error: function () {
                console.log(data);
            }
        });
    }

    function Courier(e, orderId) {
        let courierId = e.target.value;
        const selectEl = e.target;

        if (courierId === "0") return; // Seçim yapılmadıysa işlem yapma

        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1 mt-2" role="status"></div>
        <small class="mt-2">Kurye Atanıyor...</small>
    `;
        selectEl.parentNode.appendChild(loadingSpan);

        $.ajax({
            type: 'GET',
            url: '/{{$key}}/orders/sendCourier/' + orderId + '/' + courierId,
            dataType: 'json', // JSON beklediğimizi belirttik
            success: function (data) {
                loadingSpan.remove();

                if (data.success) {
                    const modalElement = document.getElementById('Courier' + orderId);
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);

                    // 1. Önce modalı kapatmayı dene
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // 2. Bootstrap'in temizlik yapması için 150ms bekle, sonra zorla temizle
                    setTimeout(() => {
                        $('.modal-backdrop').remove(); // Kalan gri katmanı sil
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });

                        // 3. Ekran temizlendikten SONRA tabloyu yenile
                        fetchOrders();
                    }, 150);

                    Swal.fire({
                        title: data.message.toUpperCase(),
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        background: '#ffffff',
                        customClass: {
                            popup: '!rounded-[32px] !border-0 !shadow-2xl',
                            title: '!font-black !tracking-tighter !text-slate-800 !text-xl',
                            timerProgressBar: '!bg-brand' // Senin ana marka rengin
                        }
                    });
                }
            },
            error: function (xhr) {
                loadingSpan.remove();
                let errorMsg = 'İşlem sırasında bir hata oluştu!';

                // Backend'den gelen 400 vb. hataların mesajını oku
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: 'ÜZGÜNÜZ :(',
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
        // İlk olarak, sayıya çevrilmeli
        const km = parseFloat(distanceKm);
        if (isNaN(km)) {
            return 'Geçersiz mesafe'; // veya başka uygun bir çıktı
        }

        if (km >= 1) {
            return `${km.toFixed(2)} km`;
        } else if (km >= 0.001) {
            return `${(km * 1000).toFixed(2)} m`;
        } else {
            return `${(km * 100000).toFixed(2)} cm`;
        }
    }

    async function generateOrderRowHtml(order) {
        const couriers = await fetchCouriers();

        const restaurantName = order.restaurant ? order.restaurant.restaurant_name : 'İsim Yok';
        const trackingId = order.tracking_id || '';
        const fullName = order.full_name || '';
        const message = order.message || '';
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

        const courierName = order.courier ? order.courier.name : 'Kurye Bulunmuyor';
        const status = order.status;
        const distanceStr = order.distance ? formatDistance(order.distance) : '';

        let platformHtml = '';
        const basePlatformClass = "inline-flex items-center gap-2 bg-white border border-slate-100 px-3 py-1.5 rounded-2xl shadow-sm transition-all hover:shadow-md";

        if (platform.toLowerCase() === 'yemeksepeti') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height:14px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'getir') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/platforms/getir.png') }}" style="height:28px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'gpsyemek') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/platforms/gpsyemek.png') }}" style="height:20px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'trendyol') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/platforms/trendyol.png') }}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'migros') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{asset('theme/images/platforms/migros.png')}}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'adisyo') {
            platformHtml = `<div class="${basePlatformClass}"><img src="{{ asset('theme/images/adisyoFull.png') }}" style="height:16px;"><span class="text-[11px] font-black text-slate-700 tracking-tighter uppercase">${restaurantName}</span></div>`;
        } else if (platform.toLowerCase() === 'telefonsiparis') {
            platformHtml = `<div class="inline-flex items-center justify-center bg-slate-900 text-white border border-slate-800 px-4 py-2 rounded-2xl shadow-sm w-full"><span class="text-[10px] font-black tracking-widest uppercase">${restaurantName} / POS</span></div>`;
        } else {
            platformHtml = `<span class="bg-slate-100 text-slate-600 text-[10px] font-black px-3 py-2 rounded-xl uppercase tracking-widest">${restaurantName}</span>`;
        }

        let courierSection = '';
        let courierStatusBadge = '';

        if (status === 'UNSUPPLIED' || status === 'DELIVERED' || status === 'HANDOVER' || '{{$key == 'restaurant'}}') {
            courierSection = `
            <a style="cursor:pointer;" class="flex items-center gap-2 text-brand font-black text-xs no-underline hover:opacity-80">
                <div class="w-8 h-8 bg-brand/10 rounded-xl flex items-center justify-center"><i class="fas fa-truck text-[10px]"></i></div>
                ${order.courier ? order.courier.name.substr(0, 10) : 'Kurye Yok'}
            </a>`;
        } else {
            if (order.courier && order.courier.id) {
                if (status === 'ASSIGNED') {
                    courierStatusBadge = '<span class="inline-block mt-1 px-2 py-0.5 bg-green-500 text-white text-[9px] font-black rounded-lg uppercase tracking-tighter"><i class="fas fa-check-double text-[8px] mr-1"></i> Paket Kabul Edildi</span>';
                } else if (status === 'PREPARED') {
                    courierStatusBadge = '<span class="inline-block mt-1 px-2 py-0.5 bg-brand text-white text-[9px] font-black rounded-lg uppercase tracking-tighter"><i class="fas fa-clock text-[8px] mr-1"></i> Teslimat Bekliyor</span>';
                }

                courierSection = `
                <div class="flex flex-col items-start group">
                    <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" style="cursor:pointer;" class="flex items-center gap-2 text-brand font-black text-xs no-underline group-hover:scale-105 transition-transform">
                       <div class="w-8 h-8 bg-brand rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand/20"><i class="fas fa-truck text-[10px]"></i></div>
                       ${order.courier.name.substr(0, 15)}
                    </a>
                    ${courierStatusBadge}
                </div>`;
            } else {
                courierSection = `
                <button data-bs-toggle="modal" data-bs-target="#Courier${order.id}" class="flex items-center gap-2 px-4 py-2 bg-white border-2 border-dashed border-slate-200 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:border-brand hover:text-brand transition-all">
                    <i class="fas fa-plus-circle"></i> KURYE ATA
                </button>`;
            }
        }

        return `
        <tr id="data_${order.id}" class="hover:bg-slate-50/50 transition-colors border-b border-slate-50">
            <td class="py-4 px-3">${platformHtml}<input type="hidden" value="${trackingId}" id="tracking_${order.id}"></td>
            <td class="py-4 px-3"><span class="font-black text-slate-400 text-xs tracking-widest">#${trackingId}</span></td>
            <td class="py-4 px-3 text-[11px] font-bold text-slate-500 italic">${order.platform_date ?? createdAt}</td>
            <td class="py-4 px-3" style="width:200px;"><span class="font-black text-slate-800 text-xs uppercase tracking-tighter truncate block">${fullName}</span></td>
            <td class="py-4 px-3">
                ${courierSection}
                <div class="modal fade" data-bs-backdrop="false"  id="Courier${order.id}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content !rounded-[32px] border-0 shadow-2xl">
                            <div class="modal-header border-0 p-6 pb-0">
                                <h5 class="text-sm font-black text-slate-800 tracking-tighter uppercase"><span class="text-brand">#${trackingId}</span> Kurye Ata</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-6">
                                <select class="form-select !rounded-2xl border-0 bg-slate-100 font-bold text-slate-700 py-3 shadow-sm" onchange="Courier(event, ${order.id})">
                                    <option value="0">Kurye Seçiniz</option>
                                    ${(order.courier_id && order.courier_id != -1 && order.courier_id != 0) ? '<option value="-1" class="text-red-500 font-black italic">Kurye Boşa Çıkar</option>' : ''}
                                    ${couriers.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                                </select>
                            </div>
                            <div class="modal-footer border-0 p-6 pt-0">
                                <button type="button" class="w-full py-3 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest border-0" data-bs-dismiss="modal">Kapat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-4 px-3 font-black text-slate-800 text-xs text-ov">${total} ₺</td>
            <td class="py-4 px-3 font-bold text-red-400 text-[10px] italic text-ov">-${discount} ₺</td>
            <td class="py-4 px-3 text-ov"><span class="px-3 py-1.5 bg-slate-900 text-white rounded-xl font-black text-xs shadow-lg shadow-slate-200">${amount} ₺</span></td>
            <td class="py-4 px-3 text-ov"><span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">${order.payment_method}</span></td>
            <td class="py-4 px-3 text-ov"><strong class="text-slate-900 font-black italic text-[10px]" id="distance${order.id}">${distanceStr}</strong></td>
            <td class="py-4 px-3">
                <input type="hidden" id="tracking_${order.id}" value="${trackingId}">
                <input type="hidden" id="platform_${order.id}" value="${platform}">

                <div class="flex flex-col gap-1 w-28" id="action-container-${order.id}">
                    ${status === 'PENDING' ? `<button class="w-full py-2 bg-brand text-white rounded-xl font-black text-[10px] uppercase tracking-tighter shadow-lg shadow-brand/20 hover:scale-105 transition-transform border-0" onclick="updateStatusDirectly('${order.id}', 'PREPARED')">Hazırlandı</button>` : ''}
                    ${status === 'HANDOVER' ? `<button class="w-full py-2 bg-green-500 text-white rounded-xl font-black text-[10px] uppercase tracking-tighter shadow-lg shadow-green-500/20 hover:scale-105 transition-transform border-0" onclick="updateStatusDirectly('${order.id}', 'DELIVERED')">Teslim Edildi</button>` : ''}
                    ${status !== 'DELIVERED' && status !== 'UNSUPPLIED'
            ? `<button class="w-full py-2 border border-red-100 text-red-500 rounded-xl font-black text-[10px] uppercase tracking-tighter hover:bg-red-50 transition-colors bg-white" onclick="handleCancelClick('${order.id}', '${platform}', '${trackingId}', event)">İptal Et</button>`
            : `<span class="bg-slate-50 text-slate-300 text-center py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest border border-slate-100">${status === 'DELIVERED' ? 'Tamamlandı' : 'İptal Edildi'}</span>`
        }
                </div>

                 <div class="modal fade" id="cancelModal${order.id}"  tabindex="-1"  aria-hidden="true"  data-bs-backdrop="static" style="z-index: 9999;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content !rounded-[32px] border-0 shadow-2xl overflow-hidden">
                            <div class="modal-header bg-red-600 text-white border-0 p-6">
                                <h5 class="text-sm font-black tracking-widest uppercase m-0">Siparişi İptal Et</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-6">
                                <div id="reasonSelectionArea${order.id}" class="mb-4"></div>
                                <div class="form-group">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Opsiyonel Not</label>
                                    <textarea class="form-control !rounded-2xl border-0 bg-slate-100 p-4 font-bold text-slate-700 shadow-inner" id="cancelReason${order.id}" rows="3" placeholder="Eklemek istediğiniz notu yazın..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-6 pt-0 flex gap-2">
                                <button type="button" class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest border-0" data-bs-dismiss="modal">Vazgeç</button>
                                <button type="button" class="flex-1 py-3 bg-red-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-600/20 border-0" onclick="confirmCancel('${order.id}','${order.tracking_id}','${order.platform}')">İptali Onayla</button>
                            </div>
                        </div>
                    </div>
                </div>


            </td>
            <td class="py-4 px-3">
                <div class="flex items-center gap-1.5">
                    <button class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand hover:border-brand shadow-sm transition-all active:scale-90"
                       onclick='openOrderModal(${JSON.stringify(order)})'>
                        <i class="fas fa-eye text-xs"></i>
                    </button>
                    <button onclick="printOrder(${order.id})" class="w-9 h-9 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-red-500 hover:border-red-500 shadow-sm transition-all active:scale-90 cursor-pointer">
                        <i class="fas fa-print text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }
</script>

