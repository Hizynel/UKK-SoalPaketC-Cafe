import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { PesananItem } from '../mockData';
import './CustomerHistoryPage.css';

interface CustomerHistoryPageProps {
  orders: PesananItem[];
  onLogout: () => void;
}

const CustomerHistoryPage: React.FC<CustomerHistoryPageProps> = ({ orders, onLogout }) => {
  const [activeFilter, setActiveFilter] = useState<'Semua' | 'Baru' | 'Diproses' | 'Selesai'>('Semua');

  const filteredOrders = orders.filter((o) => {
    if (activeFilter === 'Semua') return true;
    return o.status === activeFilter;
  });

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <header className="page-header">
            <span className="page-title">Riwayat Pesanan</span>
            <button type="button" className="header-logout-btn" onClick={onLogout}>
              Keluar
            </button>
          </header>

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
              <div className="empty-state">Belum ada riwayat pesanan.</div>
            ) : (
              filteredOrders.map((order) => {
                const isDetailed = order.items && order.items.length > 0;
                return (
                  <div key={order.id} className="history-card">
                    {/* Header Card */}
                    <div className="history-card-header">
                      <div className="history-code-title">{order.kode}</div>
                      <span className={`status-pill badge-${order.status.toLowerCase()}`}>
                        {order.status}
                      </span>
                    </div>

                    <div className="history-meta">
                      {order.meja} · {order.metode_pembayaran}
                      {order.total && !isDetailed && ` · Rp ${order.total.toLocaleString('id-ID')}`}
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

                    {/* Breakdown Item Pesanan jika ada */}
                    {isDetailed && (
                      <div className="history-breakdown">
                        {order.items.map((item, idx) => (
                          <div key={idx} className="breakdown-row">
                            <span>
                              {item.qty}× {item.nama}
                            </span>
                            <span>{(item.qty * item.harga).toLocaleString('id-ID')}</span>
                          </div>
                        ))}
                        <div className="breakdown-row">
                          <span>Service 5%</span>
                          <span>{order.service_charge.toLocaleString('id-ID')}</span>
                        </div>
                        <div className="breakdown-row">
                          <span>Pajak 10%</span>
                          <span>{order.pajak.toLocaleString('id-ID')}</span>
                        </div>
                        <div className="breakdown-divider"></div>
                        <div className="breakdown-row breakdown-total">
                          <span>Total</span>
                          <span>Rp {order.total.toLocaleString('id-ID')}</span>
                        </div>
                      </div>
                    )}
                  </div>
                );
              })
            )}
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default CustomerHistoryPage;
