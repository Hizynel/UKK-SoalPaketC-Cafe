import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { AppHeader } from '../components/Header';
import { StatusBadge } from '../components/StatusBadge';
import { ReceiptModal } from '../components/ReceiptModal';
import { Order } from '../types';
import './CustomerHistoryPage.css';

export const CustomerHistoryPage: React.FC = () => {
  const { orders, currentUser, updateOrderStatus } = useCafe();
  const [activeFilter, setActiveFilter] = useState<'Semua' | 'Baru' | 'Diproses' | 'Selesai'>('Semua');
  const [viewReceiptOrder, setViewReceiptOrder] = useState<Order | null>(null);

  // POIN 14: Pelanggan memantau status dan struk pesanannya sendiri;
  // Pesanan orang lain tidak bisa dibuka (Hanya filter berdasarkan currentUser.nomorHp)
  const myOrders = orders.filter((o) => o.pelangganHp === currentUser?.nomorHp);

  const filteredOrders = myOrders.filter((o) => {
    if (activeFilter === 'Semua') return true;
    return o.status === activeFilter;
  });

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <AppHeader
            title="Riwayat Pesanan"
            subtitle={`Akun: ${currentUser?.nama} (${currentUser?.nomorHp})`}
          />

          {/* Filter Pills */}
          <div className="filter-pills-row">
            {(['Semua', 'Baru', 'Diproses', 'Selesai'] as const).map((status) => (
              <button
                key={status}
                className={`pill-button ${activeFilter === status ? 'active' : ''}`}
                onClick={() => setActiveFilter(status)}
              >
                {status}
              </button>
            ))}
          </div>

          {/* List Riwayat Card */}
          <div className="history-list-container">
            {filteredOrders.length === 0 ? (
              <div className="empty-state">
                <div style={{ fontSize: 36, marginBottom: 8 }}>📜</div>
                <p>Belum ada riwayat pesanan untuk status ini.</p>
                <span className="privacy-badge">🔒 Terenkripsi Khusus Akun Anda</span>
              </div>
            ) : (
              filteredOrders.map((order) => {
                return (
                  <div key={order.id} className="history-card">
                    {/* Header Card */}
                    <div className="history-card-header">
                      <div className="history-code-title">{order.kode}</div>
                      <StatusBadge status={order.status} />
                    </div>

                    <div className="history-meta">
                      {order.meja} · {order.metodePembayaran} · {order.createdAt}
                    </div>

                    {/* Timeline stepper jika pesanan aktif / baru */}
                    {order.status !== 'Selesai' && order.status !== 'Dibatalkan' && (
                      <div className="stepper-container">
                        <div className={`step-item ${order.status === 'Baru' ? 'active' : 'completed'}`}>
                          Baru
                        </div>
                        <div className={`step-item ${order.status === 'Diproses' ? 'active' : ''}`}>
                          Diproses
                        </div>
                        <div className="step-item">
                          Selesai
                        </div>
                      </div>
                    )}

                    {/* Breakdown Item Pesanan */}
                    <div className="history-breakdown">
                      {order.items.map((item, idx) => (
                        <div key={idx} className="breakdown-row">
                          <span>
                            {item.qty}× {item.nama}
                          </span>
                          <span>Rp {(item.qty * item.harga).toLocaleString('id-ID')}</span>
                        </div>
                      ))}
                      <div className="breakdown-row">
                        <span>Service 5%</span>
                        <span>Rp {order.serviceCharge.toLocaleString('id-ID')}</span>
                      </div>
                      <div className="breakdown-row">
                        <span>Pajak 10%</span>
                        <span>Rp {order.pajak.toLocaleString('id-ID')}</span>
                      </div>
                      <div className="breakdown-divider"></div>
                      <div className="breakdown-row breakdown-total">
                        <span>Total</span>
                        <span>Rp {order.total.toLocaleString('id-ID')}</span>
                      </div>
                    </div>

                    {/* Aksi: Buka Struk Pribadi & Batalkan Jika Baru */}
                    <div className="history-actions-row">
                      {/* Poin 14: Buka struk pesanan sendiri */}
                      <button
                        type="button"
                        className="btn-coffee-outline"
                        style={{ flex: 1, padding: '8px 12px' }}
                        onClick={() => setViewReceiptOrder(order)}
                      >
                        📄 Lihat Struk
                      </button>

                      {order.status === 'Baru' && (
                        <button
                          type="button"
                          className="btn-cancel-small"
                          onClick={() => {
                            if (window.confirm('Batalkan pesanan ini? Stok bahan akan kembali otomatis.')) {
                              updateOrderStatus(order.id, 'Dibatalkan');
                            }
                          }}
                        >
                          Batalkan
                        </button>
                      )}
                    </div>
                  </div>
                );
              })
            )}
          </div>
        </div>

        {/* Modal Struk Mandiri (Poin 14) */}
        <ReceiptModal
          order={viewReceiptOrder}
          onClose={() => setViewReceiptOrder(null)}
        />
      </IonContent>
    </IonPage>
  );
};
