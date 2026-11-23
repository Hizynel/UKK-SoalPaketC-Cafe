import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { AppHeader } from '../components/Header';
import { PriceSummary } from '../components/PriceSummary';
import { Order } from '../types';
import './CustomerOrderPage.css';

interface CustomerOrderPageProps {
  initialQuantities?: Record<number, number>;
  onOrderSuccess: (order: Order) => void;
  onGoToHistory: () => void;
}

export const CustomerOrderPage: React.FC<CustomerOrderPageProps> = ({
  initialQuantities = {},
  onOrderSuccess,
  onGoToHistory,
}) => {
  const {
    menus,
    currentUser,
    hasActiveBaruOrder,
    createOrder,
    checkStockAvailability,
    isMenuAvailable,
  } = useCafe();

  const [selectedTable, setSelectedTable] = useState('Meja 3');
  const [paymentMethod, setPaymentMethod] = useState<'Tunai' | 'QRIS'>('Tunai');
  
  // Default SEMUA item adalah 0
  const [quantities, setQuantities] = useState<Record<number, number>>(() => ({
    ...initialQuantities,
  }));
  const [errorMsg, setErrorMsg] = useState('');

  const hasActive = currentUser ? hasActiveBaruOrder(currentUser.nomorHp) : false;

  const updateQty = (id: number, delta: number) => {
    setErrorMsg('');
    setQuantities((prev) => {
      const current = prev[id] || 0;
      const next = Math.max(0, current + delta);
      return { ...prev, [id]: next };
    });
  };

  // Buat list item terpilih
  const selectedItems = Object.entries(quantities)
    .filter(([_, qty]) => qty > 0)
    .map(([idStr, qty]) => ({
      menuId: Number(idStr),
      qty,
    }));

  const subtotal = selectedItems.reduce((acc, it) => {
    const m = menus.find((menu) => menu.id === it.menuId);
    return acc + (m ? m.harga * it.qty : 0);
  }, 0);

  const serviceCharge = Math.round(subtotal * 0.05);
  const pajak = Math.round(subtotal * 0.1);
  const total = subtotal + serviceCharge + pajak;

  // Cek ketersediaan stok
  const stockCheck = checkStockAvailability(selectedItems);

  // Sorting: Menu tersedia di atas, menu habis/tanpa resep di bawah
  const sortedMenus = [...menus].sort((a, b) => {
    const aAvailable = isMenuAvailable(a.id);
    const bAvailable = isMenuAvailable(b.id);
    if (aAvailable && !bAvailable) return -1;
    if (!aAvailable && bAvailable) return 1;
    return 0;
  });

  const handleOrderSubmit = async () => {
    if (!currentUser) return;
    setErrorMsg('');

    if (hasActive) {
      setErrorMsg('Peringatan: Anda masih memiliki pesanan berstatus "Baru". Hanya boleh satu pesanan baru pada satu waktu.');
      return;
    }

    if (selectedItems.length === 0) {
      setErrorMsg('Silakan pilih minimal 1 menu dengan jumlah lebih dari 0.');
      return;
    }

    if (!stockCheck.available) {
      setErrorMsg(stockCheck.reason || 'Stok bahan baku tidak mencukupi.');
      return;
    }

    const res = await createOrder({
      pelangganHp: currentUser.nomorHp,
      pelangganNama: currentUser.nama,
      meja: selectedTable,
      metodePembayaran: paymentMethod,
      items: selectedItems,
    });

    if (!res.success) {
      setErrorMsg(res.message || 'Gagal membuat pesanan.');
    } else if (res.order) {
      onOrderSuccess(res.order);
    }
  };

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <AppHeader title="Buat Pesanan" />

          <div className="order-form-body">
            {/* Aturan Poin 13: Hanya boleh satu pesanan berstatus Baru */}
            {hasActive && (
              <div className="active-order-block-notice">
                <div className="notice-icon">⚠️</div>
                <div className="notice-text">
                  <strong>Aturan Pesanan:</strong> Anda saat ini sudah memiliki pesanan berstatus{' '}
                  <span className="badge-tag">Baru</span>. Hanya boleh 1 pesanan baru pada satu waktu.
                </div>
                <button
                  type="button"
                  className="btn-coffee-outline"
                  style={{ alignSelf: 'flex-start', marginTop: 8 }}
                  onClick={onGoToHistory}
                >
                  Pantau di Riwayat Pesanan
                </button>
              </div>
            )}

            {/* Error Message */}
            {errorMsg && <div className="error-alert-banner">{errorMsg}</div>}

            {/* Nomor Meja (1-10) */}
            <div className="order-field">
              <label className="order-field-label">Nomor Meja (1–10)</label>
              <div className="select-container">
                <select
                  value={selectedTable}
                  onChange={(e) => setSelectedTable(e.target.value)}
                  className="order-select"
                  disabled={hasActive}
                >
                  {[...Array(10)].map((_, i) => (
                    <option key={i + 1} value={`Meja ${i + 1}`}>
                      Meja {i + 1}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            {/* Metode Pembayaran */}
            <div className="order-field">
              <label className="order-field-label">Pembayaran</label>
              <div className="payment-toggle-group">
                <button
                  type="button"
                  disabled={hasActive}
                  className={`payment-toggle-btn ${paymentMethod === 'Tunai' ? 'active' : ''}`}
                  onClick={() => setPaymentMethod('Tunai')}
                >
                  Tunai
                </button>
                <button
                  type="button"
                  disabled={hasActive}
                  className={`payment-toggle-btn ${paymentMethod === 'QRIS' ? 'active' : ''}`}
                  onClick={() => setPaymentMethod('QRIS')}
                >
                  QRIS
                </button>
              </div>
            </div>

            {/* Daftar Menu (Sesuai database, yang habis di bawah & blur) */}
            <div className="order-items-list">
              {sortedMenus.map((m) => {
                const qty = quantities[m.id] || 0;
                const available = isMenuAvailable(m.id);

                return (
                  <div
                    key={m.id}
                    className={`order-item-card ${!available ? 'item-out-of-stock' : ''}`}
                  >
                    <div className="order-item-info">
                      <div className="order-item-name">
                        {m.nama}{' '}
                        {!available && <span className="out-tag">(Habis / Tidak Tersedia)</span>}
                      </div>
                      <div className="order-item-price">Rp {m.harga.toLocaleString('id-ID')}</div>
                    </div>

                    <div className="quantity-controls">
                      <button
                        type="button"
                        className="counter-btn"
                        disabled={hasActive || qty === 0 || !available}
                        onClick={() => updateQty(m.id, -1)}
                      >
                        −
                      </button>
                      <span className="counter-val">{qty}</span>
                      <button
                        type="button"
                        className="counter-btn"
                        disabled={hasActive || !available}
                        onClick={() => updateQty(m.id, 1)}
                      >
                        +
                      </button>
                    </div>
                  </div>
                );
              })}
            </div>

            {/* Status Stok Bahan Real-time */}
            {selectedItems.length > 0 && (
              <div
                className={`stock-status-badge ${stockCheck.available ? 'stock-ok' : 'stock-insufficient'
                  }`}
              >
                {stockCheck.available
                  ? '✓ Stok bahan dapur mencukupi untuk seluruh item pilihan.'
                  : `✕ ${stockCheck.reason}`}
              </div>
            )}

            {/* Ringkasan Harga (Reusable PriceSummary) */}
            <PriceSummary
              subtotal={subtotal}
              serviceCharge={serviceCharge}
              pajak={pajak}
              total={total}
            />

            {/* Tombol Buat Pesanan */}
            <button
              type="button"
              className="btn-coffee-primary"
              disabled={hasActive || selectedItems.length === 0 || !stockCheck.available}
              style={{
                marginTop: 10,
                opacity: hasActive || selectedItems.length === 0 || !stockCheck.available ? 0.6 : 1,
              }}
              onClick={handleOrderSubmit}
            >
              {hasActive ? 'Menunggu Pesanan Baru Selesai' : 'Pesan'}
            </button>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};
