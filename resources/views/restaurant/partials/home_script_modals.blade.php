{{-- Modal Ortak Stiller --}}
<style>
    .gk-modal { z-index: 9999; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
    .gk-modal .modal-content { border: none; border-radius: 28px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden; }
    .gk-modal .modal-header { border: none; padding: 24px 28px 12px; }
    .gk-modal .modal-body { padding: 16px 28px 24px; }
    .gk-modal .modal-footer { border: none; padding: 0 28px 24px; }
    .gk-modal .modal-title { font-size: 15px; font-weight: 900; letter-spacing: -0.3px; color: #0f172a; }
    .gk-modal .modal-subtitle { font-size: 11px; font-weight: 600; color: #94a3b8; }
    .gk-btn { border: none; border-radius: 14px; padding: 11px 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; transition: all 0.2s; cursor: pointer; }
    .gk-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .gk-btn-primary { background: #4f46e5; color: #fff; }
    .gk-btn-danger { background: #dc2626; color: #fff; }
    .gk-btn-light { background: #f1f5f9; color: #64748b; }
    .gk-input { border: none; background: #f1f5f9; border-radius: 14px; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #334155; transition: box-shadow 0.2s; }
    .gk-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); background: #fff; }
    .gk-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; display: block; }
</style>

{{-- Sipariş Detay Modalı --}}
<div class="modal fade gk-modal" id="OrdersModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Sipariş Detayı</h5>
                    <span class="modal-subtitle" id="orderModalSubtitle"></span>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer d-flex gap-2">
                <button id="printOrderBtn" class="gk-btn gk-btn-primary flex-fill">
                    <i class="fas fa-print me-2"></i>YAZDIR
                </button>
                <button type="button" class="gk-btn gk-btn-light" data-bs-dismiss="modal">KAPAT</button>
            </div>
        </div>
    </div>
</div>

{{-- Kurye Atama Modalı --}}
<div class="modal fade gk-modal" id="sharedCourierModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="fas fa-motorcycle me-2" style="color:#4f46e5;"></i>Kurye Ata</h5>
                    <span class="modal-subtitle" id="courierModalOrderInfo"></span>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Arama --}}
                <div class="position-relative mb-3">
                    <i class="fas fa-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); font-size:11px; color:#94a3b8;"></i>
                    <input type="text" id="courierSearchInput" class="form-control gk-input" style="padding-left:38px;" placeholder="Kurye ara..." oninput="filterCourierList()">
                </div>

                {{-- Kurye Boşa Çıkar --}}
                <div id="courierRemoveBtn" class="d-none mb-3">
                    <button class="gk-btn w-100 d-flex align-items-center justify-content-center gap-2" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;" onclick="assignCourierToOrder(-1)">
                        <i class="fas fa-user-slash" style="font-size:11px;"></i> Kuryeyi Kaldır
                    </button>
                </div>

                {{-- Kurye Listesi --}}
                <div id="courierListContainer" style="max-height: 340px; overflow-y: auto; margin: 0 -8px; padding: 0 8px;">
                    <div class="text-center py-5">
                        <div class="spinner-border spinner-border-sm" role="status" style="color:#4f46e5;"></div>
                        <p class="mt-2 mb-0" style="font-size:11px; font-weight:700; color:#94a3b8;">Kuryeler yükleniyor...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="gk-btn gk-btn-light w-100" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

{{-- İptal Modalı --}}
<div class="modal fade gk-modal" id="sharedCancelModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, #dc2626, #b91c1c); padding:20px 28px;">
                <div>
                    <h5 style="font-size:14px; font-weight:900; color:#fff; letter-spacing:0.5px; margin:0;">Siparişi İptal Et</h5>
                    <span style="font-size:11px; color:rgba(255,255,255,0.7); font-weight:600;" id="cancelModalOrderInfo"></span>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sharedReasonSelectionArea" class="mb-3"></div>
                <div>
                    <label class="gk-label">Opsiyonel Not</label>
                    <textarea class="form-control gk-input" id="sharedCancelReason" rows="3" placeholder="Eklemek istediğiniz notu yazın..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="gk-btn gk-btn-light flex-fill" data-bs-dismiss="modal">Vazgeç</button>
                <button type="button" class="gk-btn gk-btn-danger flex-fill" id="sharedCancelConfirmBtn">
                    <i class="fas fa-ban me-1"></i>İptali Onayla
                </button>
            </div>
        </div>
    </div>
</div>
