import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { MenuItem, PesananItem } from '../mockData';
import './CustomerOrderPage.css';

interface CustomerOrderPageProps {
  menus: MenuItem[];
  onOrderSuccess: (order: PesananItem) => void;
  onLogout: () => void;
}

const CustomerOrderPage: React.FC<CustomerOrderPageProps> = ({ menus, onOrderSuccess, onLogout }) => {
  const [selectedTable, setSelectedTable] = useState('Meja 3');
  const [paymentMethod, setPaymentMethod] = useState<'Tunai' | 'QRIS'>('Tunai');
  const [quantities, setQuantities] = useState<Record<number, number>>({
    1: 2, // Kopi Susu Senja: 2
    4: 1, // Roti Bakar Cokelat: 1
    2: 0, // Americano: 0
  });

  const updateQty = (id: number, delta: number) => {
    setQuantities((prev) => {
      const current = prev[id] || 0;
      const next = Math.max(0, current + delta);
      return { ...prev, [id]: next };
    });
  };

  // Hitung subtotal
  const subtotal = menus.reduce((acc, m) => {
    const qty = quantities[m.id] || 0;
    return acc + m.harga * qty;
  }, 0);

  const serviceCharge = Math.round(subtotal * 0.05);
  const pajak = Math.round(subtotal * 0.1);
  const total = subtotal + serviceCharge + pajak;

  const handleCreateOrder = () => {
    if (subtotal === 0) {
      alert('Silakan pilih minimal 1 menu!');
      return;
    }

    // Generate kode unik acak seperti K7M2QX4A
    const randomCode = Math.random().toString(36).substring(2, 10).toUpperCase();

    const orderItems = menus
      .filter((m) => (quantities[m.id] || 0) > 0)
      .map((m) => ({
        nama: m.nama,
        qty: quantities[m.id] || 0,
        harga: m.harga,
      }));

    const newOrder: PesananItem = {
      id: Date.now(),
      kode: randomCode,
      pelanggan: 'Rina Wulandari',
      meja: selectedTable,
      metode_pembayaran: paymentMethod,
      items: orderItems,
      subtotal,
      service_charge: serviceCharge,
      pajak,
      total,
      status: 'Baru',
      waktu: 'Baru saja',
    };

    onOrderSuccess(newOrder);
  };

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <header className="page-header">
            <span className="page-title">Buat Pesanan</span>
            <button type="button" className="header-logout-btn" onClick={onLogout}>
              Keluar
            </button>
          </header>

          <div className="order-form-body">
            {/* Nomor Meja */}
            <div className="order-field">
              <label className="order-field-label">Nomor Meja (1–10)</label>
              <div className="select-container">
                <select
                  value={selectedTable}
                  onChange={(e) => setSelectedTable(e.target.value)}
                  className="order-select"
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
                  className={`payment-toggle-btn ${paymentMethod === 'Tunai' ? 'active' : ''}`}
                  onClick={() => setPaymentMethod('Tunai')}
                >
                  Tunai
                </button>
                <button
                  type="button"
                  className={`payment-toggle-btn ${paymentMethod === 'QRIS' ? 'active' : ''}`}
                  onClick={() => setPaymentMethod('QRIS')}
                >
                  QRIS
                </button>
              </div>
            </div>

            {/* Menu Items with Counter */}
            <div className="order-items-list">
              {menus
                .filter((m) => m.status === 'tersedia')
                .map((m) => {
                  const qty = quantities[m.id] || 0;
                  return (
                    <div key={m.id} className="order-item-card">
                      <div className="order-item-info">
                        <div className="order-item-name">{m.nama}</div>
                        <div className="order-item-price">Rp {m.harga.toLocaleString('id-ID')}</div>
                      </div>

                      <div className="quantity-controls">
                        <button
                          type="button"
                          className="counter-btn"
                          onClick={() => updateQty(m.id, -1)}
                        >
                          −
                        </button>
                        <span className="counter-val">{qty}</span>
                        <button
                          type="button"
                          className="counter-btn"
                          onClick={() => updateQty(m.id, 1)}
                        >
                          +
                        </button>
                      </div>
                    </div>
                  );
                })}
            </div>

            {/* Price Summary */}
            <div className="price-summary-box">
              <div className="summary-row">
                <span>Subtotal</span>
                <span>Rp {subtotal.toLocaleString('id-ID')}</span>
              </div>
              <div className="summary-row">
                <span>Service 5%</span>
                <span>Rp {serviceCharge.toLocaleString('id-ID')}</span>
              </div>
              <div className="summary-row">
                <span>Pajak 10%</span>
                <span>Rp {pajak.toLocaleString('id-ID')}</span>
              </div>
              <div className="summary-divider"></div>
              <div className="summary-row summary-total">
                <span>Total</span>
                <span>Rp {total.toLocaleString('id-ID')}</span>
              </div>
            </div>

            {/* Button Buat Pesanan */}
            <button
              type="button"
              className="btn-coffee-primary"
              style={{ marginTop: 16 }}
              onClick={handleCreateOrder}
            >
              Pesan
            </button>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default CustomerOrderPage;

