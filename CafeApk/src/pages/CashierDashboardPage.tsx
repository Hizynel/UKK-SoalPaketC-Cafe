import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { MenuItem, PesananItem } from '../mockData';
import './CashierDashboardPage.css';

interface CashierDashboardPageProps {
  orders: PesananItem[];
  menus: MenuItem[];
  onUpdateStatus: (orderId: number, nextStatus: 'Diproses' | 'Selesai' | 'Dibatalkan') => void;
  onLogout: () => void;
}

const CashierDashboardPage: React.FC<CashierDashboardPageProps> = ({
  orders,
  onUpdateStatus,
  onLogout,
}) => {
  const [activeTab, setActiveTab] = useState<'dashboard' | 'input' | 'data' | 'pelanggan'>('dashboard');
  const [selectedUserReset, setSelectedUserReset] = useState<string | null>(null);
  const [resetCode, setResetCode] = useState('');

  // Hitung metric
  const countBaru = orders.filter((o) => o.status === 'Baru').length;
  const countDiproses = orders.filter((o) => o.status === 'Diproses').length;
  const countSelesai = orders.filter((o) => o.status === 'Selesai').length;

  const handleResetPassword = (name: string) => {
    const code = Math.random().toString(36).substring(2, 10).toUpperCase();
    setResetCode(code);
    setSelectedUserReset(name);
  };

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="cashier-container">
          {/* Topbar */}
          <div className="cashier-topbar">
            <div className="cashier-brand">
              <span className="brand-name">Kedai Kopi Senja</span>
              <span className="brand-sub">Sistem Manajemen Kafe</span>
            </div>
            <div className="cashier-user-badge">
              <span>Rina · Kasir</span>
              <button type="button" className="btn-logout-small" onClick={onLogout}>
                Keluar
              </button>
            </div>
          </div>

          {/* Sub Navigation Bar for Cashier */}
          <div className="cashier-nav-tabs">
            <button
              className={`nav-tab-btn ${activeTab === 'dashboard' ? 'active' : ''}`}
              onClick={() => setActiveTab('dashboard')}
            >
              Dashboard
            </button>
            <button
              className={`nav-tab-btn ${activeTab === 'input' ? 'active' : ''}`}
              onClick={() => setActiveTab('input')}
            >
              + Input
            </button>
            <button
              className={`nav-tab-btn ${activeTab === 'data' ? 'active' : ''}`}
              onClick={() => setActiveTab('data')}
            >
              Data Pesanan
            </button>
            <button
              className={`nav-tab-btn ${activeTab === 'pelanggan' ? 'active' : ''}`}
              onClick={() => setActiveTab('pelanggan')}
            >
              Akun
            </button>
          </div>

          {/* TAB 1: DASHBOARD */}
          {activeTab === 'dashboard' && (
            <div className="cashier-tab-content">
              {/* Stat Cards */}
              <div className="stat-cards-row">
                <div className="stat-card">
                  <span className="stat-label">Pesanan Baru</span>
                  <span className="stat-value color-brown">{countBaru}</span>
                </div>
                <div className="stat-card">
                  <span className="stat-label">Sedang Diproses</span>
                  <span className="stat-value color-blue">{countDiproses}</span>
                </div>
                <div className="stat-card">
                  <span className="stat-label">Selesai Hari Ini</span>
                  <span className="stat-value color-green">{countSelesai}</span>
                </div>
              </div>

              {/* Table Antrean Pesanan Baru */}
              <div className="section-card">
                <h3 className="section-title">Antrean Pesanan Baru</h3>
                <div className="orders-table-wrapper">
                  {orders
                    .filter((o) => o.status === 'Baru')
                    .map((item) => (
                      <div key={item.id} className="cashier-order-row">
                        <div className="order-main-info">
                          <div className="order-row-top">
                            <span className="badge-code">{item.kode}</span>
                            <span className="order-time">{item.waktu}</span>
                          </div>
                          <div className="order-customer">{item.pelanggan} ({item.meja})</div>
                          <div className="order-amount">Rp {item.total.toLocaleString('id-ID')} · {item.metode_pembayaran}</div>
                        </div>
                        <div className="order-action-buttons">
                          <button
                            type="button"
                            className="btn-action btn-process"
                            onClick={() => onUpdateStatus(item.id, 'Diproses')}
                          >
                            Proses
                          </button>
                          <button
                            type="button"
                            className="btn-action btn-cancel"
                            onClick={() => onUpdateStatus(item.id, 'Dibatalkan')}
                          >
                            Batalkan
                          </button>
                        </div>
                      </div>
                    ))}
                  {orders.filter((o) => o.status === 'Baru').length === 0 && (
                    <div className="empty-notice">Tidak ada antrean pesanan baru.</div>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* TAB 2: INPUT PESANAN */}
          {activeTab === 'input' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Masukan Pesanan</h3>
                <div className="input-form-inner">
                  <div className="form-group">
                    <label className="form-label">Nama Pelanggan</label>
                    <input type="text" className="form-input" defaultValue="Rina Wulandari" />
                  </div>
                  <div className="form-group">
                    <label className="form-label">Nomor Meja (1–10)</label>
                    <select className="form-input" defaultValue="Meja 3">
                      {[...Array(10)].map((_, i) => (
                        <option key={i + 1} value={`Meja ${i + 1}`}>
                          Meja {i + 1}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div className="form-group">
                    <label className="form-label">Metode Pembayaran</label>
                    <div className="payment-toggle-group">
                      <button type="button" className="payment-toggle-btn active">
                        Tunai
                      </button>
                      <button type="button" className="payment-toggle-btn">
                        QRIS
                      </button>
                    </div>
                  </div>

                  <div className="form-group" style={{ marginTop: 8 }}>
                    <label className="form-label">Item Pesanan</label>
                    <div className="select-qty-row">
                      <select className="form-input" defaultValue="1" style={{ flex: 1 }}>
                        <option value="1">Kopi Susu Senja — Rp 22.000</option>
                        <option value="2">Americano — Rp 18.000</option>
                        <option value="4">Roti Bakar Cokelat — Rp 20.000</option>
                      </select>
                      <input type="number" className="form-input" defaultValue="2" style={{ width: 60, textAlign: 'center' }} />
                    </div>
                  </div>

                  {/* Stock banner */}
                  <div className="stock-badge-info">
                    ✓ Stok bahan mencukupi untuk seluruh item.
                  </div>

                  {/* Price Summary */}
                  <div className="price-summary-box">
                    <div className="summary-row">
                      <span>Subtotal</span>
                      <span>Rp 64.000</span>
                    </div>
                    <div className="summary-row">
                      <span>Service charge (5%)</span>
                      <span>Rp 3.200</span>
                    </div>
                    <div className="summary-row">
                      <span>Pajak (10%)</span>
                      <span>Rp 6.720</span>
                    </div>
                    <div className="summary-divider"></div>
                    <div className="summary-row summary-total">
                      <span>Total Akhir</span>
                      <span>Rp 73.920</span>
                    </div>
                  </div>

                  <button
                    type="button"
                    className="btn-coffee-primary"
                    style={{ marginTop: 14 }}
                    onClick={() => {
                      alert('Pesanan berhasil disimpan oleh kasir!');
                      setActiveTab('dashboard');
                    }}
                  >
                    Simpan Pesanan
                  </button>
                </div>
              </div>
            </div>
          )}

          {/* TAB 3: DATA PESANAN / RIWAYAT */}
          {activeTab === 'data' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Data Pesanan / Riwayat</h3>
                <p className="status-flow-hint">
                  Status berubah berjenjang: <b>Baru → Diproses → Selesai</b>. Pembatalan hanya untuk pesanan Baru dan stok bahan kembali otomatis.
                </p>

                <div className="orders-table-wrapper">
                  {orders.map((item) => (
                    <div key={item.id} className="cashier-order-row">
                      <div className="order-main-info">
                        <div className="order-row-top">
                          <span className="badge-code">{item.kode}</span>
                          <span className={`status-pill badge-${item.status.toLowerCase()}`}>
                            {item.status}
                          </span>
                        </div>
                        <div className="order-customer">
                          {item.pelanggan} · {item.meja}
                        </div>
                        <div className="order-amount">
                          Rp {item.total.toLocaleString('id-ID')} · {item.metode_pembayaran}
                        </div>
                      </div>

                      <div className="order-action-buttons">
                        {item.status === 'Baru' && (
                          <>
                            <button
                              type="button"
                              className="btn-action btn-process"
                              onClick={() => onUpdateStatus(item.id, 'Diproses')}
                            >
                              Proses
                            </button>
                            <button
                              type="button"
                              className="btn-action btn-cancel"
                              onClick={() => onUpdateStatus(item.id, 'Dibatalkan')}
                            >
                              Batalkan
                            </button>
                          </>
                        )}
                        {item.status === 'Diproses' && (
                          <button
                            type="button"
                            className="btn-action btn-finish"
                            onClick={() => onUpdateStatus(item.id, 'Selesai')}
                          >
                            Selesaikan
                          </button>
                        )}
                        {item.status === 'Selesai' && (
                          <button
                            type="button"
                            className="btn-action btn-receipt"
                            onClick={() => alert(`Struk ${item.kode} siap dicetak.`)}
                          >
                            Lihat Struk
                          </button>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* TAB 4: AKUN PELANGGAN */}
          {activeTab === 'pelanggan' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Manajemen Akun Pelanggan</h3>
                <div className="search-box-wrap">
                  <input
                    type="text"
                    className="form-input"
                    placeholder="Cari nama atau nomor HP..."
                  />
                </div>

                <div className="customer-acc-list">
                  {[
                    { name: 'Rina Wulandari', hp: '081234567890', date: '02 Nov 2026' },
                    { name: 'Dimas Pratama', hp: '085711223344', date: '03 Nov 2026' },
                    { name: 'Sari Anggraini', hp: '082198765432', date: '05 Nov 2026' },
                    { name: 'Budi Santoso', hp: '081377889900', date: '06 Nov 2026' },
                    { name: 'Ayu Lestari', hp: '087855443322', date: '08 Nov 2026' },
                  ].map((cust, i) => (
                    <div key={i} className="customer-acc-item">
                      <div className="customer-acc-info">
                        <div className="customer-acc-name">{cust.name}</div>
                        <div className="customer-acc-sub">
                          {cust.hp} · Terdaftar: {cust.date}
                        </div>
                      </div>
                      <button
                        type="button"
                        className="btn-reset-pwd"
                        onClick={() => handleResetPassword(cust.name)}
                      >
                        Reset Password
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* POPUP MODAL RESET PASSWORD */}
          {selectedUserReset && (
            <div className="modal-backdrop">
              <div className="modal-content-card">
                <h3 className="modal-title">Password sementara dibuat</h3>
                <p className="modal-desc">
                  Berikan ke <b>{selectedUserReset}</b>. Pelanggan wajib menggantinya saat login berikutnya.
                </p>
                <div className="temp-code-badge">{resetCode}</div>
                <button
                  type="button"
                  className="btn-coffee-primary"
                  onClick={() => setSelectedUserReset(null)}
                >
                  Selesai
                </button>
              </div>
            </div>
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};

export default CashierDashboardPage;

