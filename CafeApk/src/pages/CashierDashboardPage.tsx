import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { StatusBadge } from '../components/StatusBadge';
import { PriceSummary } from '../components/PriceSummary';
import { ReceiptModal } from '../components/ReceiptModal';
import { Order } from '../types';
import './CashierDashboardPage.css';

export const CashierDashboardPage: React.FC = () => {
  const {
    orders,
    menus,
    users,
    bahanBaku,
    currentUser,
    logout,
    updateOrderStatus,
    createOrder,
    checkStockAvailability,
    resetCustomerPasswordByCashier,
  } = useCafe();

  const [activeTab, setActiveTab] = useState<'dashboard' | 'input' | 'data' | 'pelanggan' | 'stok'>('dashboard');

  // Input Pesanan Kasir state (default semua qty 0)
  const [cashierCustName, setCashierCustName] = useState('Pelanggan Meja 3');
  const [cashierTable, setCashierTable] = useState('Meja 3');
  const [cashierPayMethod, setCashierPayMethod] = useState<'Tunai' | 'QRIS'>('Tunai');
  const [cashierQuantities, setCashierQuantities] = useState<Record<number, number>>({});

  // Modal Reset Password Kasir
  const [selectedUserReset, setSelectedUserReset] = useState<string | null>(null);
  const [resetCode, setResetCode] = useState('');

  // Modal Struk
  const [receiptOrder, setReceiptOrder] = useState<Order | null>(null);

  // Search Akun
  const [searchUser, setSearchUser] = useState('');

  // Hitung metrik
  const countBaru = orders.filter((o) => o.status === 'Baru').length;
  const countDiproses = orders.filter((o) => o.status === 'Diproses').length;
  const countSelesai = orders.filter((o) => o.status === 'Selesai').length;

  // Kasir Reset Password (Poin 12)
  const handleTriggerReset = async (hp: string, name: string) => {
    const res = await resetCustomerPasswordByCashier(hp);
    if (res.success && res.tempPassword) {
      setResetCode(res.tempPassword);
      setSelectedUserReset(name);
    }
  };

  // Kasir Buat Pesanan (Poin 13: Aturan pesanan & stok sama dengan pesanan kasir)
  const cashierSelectedItems = Object.entries(cashierQuantities)
    .filter(([_, qty]) => qty > 0)
    .map(([id, qty]) => ({ menuId: Number(id), qty }));

  const cashierSubtotal = cashierSelectedItems.reduce((acc, it) => {
    const m = menus.find((menu) => menu.id === it.menuId);
    return acc + (m ? m.harga * it.qty : 0);
  }, 0);
  const cashierService = Math.round(cashierSubtotal * 0.05);
  const cashierPajak = Math.round(cashierSubtotal * 0.1);
  const cashierTotal = cashierSubtotal + cashierService + cashierPajak;

  const stockCheck = checkStockAvailability(cashierSelectedItems);

  const handleCashierSubmitOrder = async () => {
    if (cashierSelectedItems.length === 0) {
      alert('Pilih minimal 1 item.');
      return;
    }
    if (!stockCheck.available) {
      alert(stockCheck.reason || 'Stok bahan tidak mencukupi.');
      return;
    }

    const res = await createOrder({
      pelangganHp: 'walkin-' + Date.now(),
      pelangganNama: cashierCustName,
      meja: cashierTable,
      metodePembayaran: cashierPayMethod,
      items: cashierSelectedItems,
    });

    if (res.success) {
      alert('Pesanan kasir berhasil disimpan!');
      setActiveTab('dashboard');
    } else {
      alert(res.message);
    }
  };

  const filteredCustomerAccounts = users.filter(
    (u) =>
      u.role === 'pelanggan' &&
      (u.nama.toLowerCase().includes(searchUser.toLowerCase()) || u.nomorHp.includes(searchUser))
  );

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
              <span>{currentUser?.nama || 'Kasir'}</span>
              <button type="button" className="btn-logout-small" onClick={logout}>
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
              Akun Pelanggan
            </button>
            <button
              className={`nav-tab-btn ${activeTab === 'stok' ? 'active' : ''}`}
              onClick={() => setActiveTab('stok')}
            >
              Stok Bahan
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
                            <span className="order-time">{item.createdAt}</span>
                          </div>
                          <div className="order-customer">
                            {item.pelangganNama} ({item.meja})
                          </div>
                          <div className="order-amount">
                            Rp {item.total.toLocaleString('id-ID')} · {item.metodePembayaran}
                          </div>
                        </div>
                        <div className="order-action-buttons">
                          <button
                            type="button"
                            className="btn-action btn-process"
                            onClick={() => updateOrderStatus(item.id, 'Diproses')}
                          >
                            Proses
                          </button>
                          <button
                            type="button"
                            className="btn-action btn-cancel"
                            onClick={() => updateOrderStatus(item.id, 'Dibatalkan')}
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

          {/* TAB 2: INPUT PESANAN KASIR */}
          {activeTab === 'input' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Masukan Pesanan</h3>
                <div className="input-form-inner">
                  <div className="form-group">
                    <label className="form-label">Nama Pelanggan</label>
                    <input
                      type="text"
                      className="form-input"
                      value={cashierCustName}
                      onChange={(e) => setCashierCustName(e.target.value)}
                    />
                  </div>

                  <div className="form-group">
                    <label className="form-label">Nomor Meja (1–10)</label>
                    <select
                      className="form-input"
                      value={cashierTable}
                      onChange={(e) => setCashierTable(e.target.value)}
                    >
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
                      <button
                        type="button"
                        className={`payment-toggle-btn ${cashierPayMethod === 'Tunai' ? 'active' : ''}`}
                        onClick={() => setCashierPayMethod('Tunai')}
                      >
                        Tunai
                      </button>
                      <button
                        type="button"
                        className={`payment-toggle-btn ${cashierPayMethod === 'QRIS' ? 'active' : ''}`}
                        onClick={() => setCashierPayMethod('QRIS')}
                      >
                        QRIS
                      </button>
                    </div>
                  </div>

                  {/* Pilih Menu */}
                  <div className="form-group" style={{ marginTop: 8 }}>
                    <label className="form-label">Item Pesanan</label>
                    {menus.map((m) => (
                      <div key={m.id} style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f2e9e1' }}>
                        <div>
                          <div style={{ fontWeight: 600, fontSize: 14 }}>{m.nama}</div>
                          <div style={{ fontSize: 12, color: '#796e68' }}>Rp {m.harga.toLocaleString('id-ID')}</div>
                        </div>
                        <input
                          type="number"
                          min="0"
                          className="form-input"
                          style={{ width: 64, textAlign: 'center' }}
                          value={cashierQuantities[m.id] || 0}
                          onChange={(e) =>
                            setCashierQuantities((prev) => ({
                              ...prev,
                              [m.id]: Math.max(0, parseInt(e.target.value) || 0),
                            }))
                          }
                        />
                      </div>
                    ))}
                  </div>

                  {/* Stock banner */}
                  <div
                    className="stock-badge-info"
                    style={{
                      backgroundColor: stockCheck.available ? '#e5f6ee' : '#fde8e8',
                      color: stockCheck.available ? '#17834a' : '#d33c30',
                    }}
                  >
                    {stockCheck.available
                      ? '✓ Stok bahan mencukupi untuk seluruh item.'
                      : `✕ ${stockCheck.reason}`}
                  </div>

                  {/* Price Summary */}
                  <PriceSummary
                    subtotal={cashierSubtotal}
                    serviceCharge={cashierService}
                    pajak={cashierPajak}
                    total={cashierTotal}
                  />

                  <button
                    type="button"
                    className="btn-coffee-primary"
                    disabled={!stockCheck.available || cashierSelectedItems.length === 0}
                    style={{
                      marginTop: 14,
                      opacity: !stockCheck.available || cashierSelectedItems.length === 0 ? 0.6 : 1,
                    }}
                    onClick={handleCashierSubmitOrder}
                  >
                    Simpan Pesanan
                  </button>
                </div>
              </div>
            </div>
          )}

          {/* TAB 3: DATA PESANAN / RIWAYAT KASIR */}
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
                          <StatusBadge status={item.status} />
                        </div>
                        <div className="order-customer">
                          {item.pelangganNama} · {item.meja}
                        </div>
                        <div className="order-amount">
                          Rp {item.total.toLocaleString('id-ID')} · {item.metodePembayaran}
                        </div>
                      </div>

                      <div className="order-action-buttons">
                        {item.status === 'Baru' && (
                          <>
                            <button
                              type="button"
                              className="btn-action btn-process"
                              onClick={() => updateOrderStatus(item.id, 'Diproses')}
                            >
                              Proses
                            </button>
                            <button
                              type="button"
                              className="btn-action btn-cancel"
                              onClick={() => updateOrderStatus(item.id, 'Dibatalkan')}
                            >
                              Batalkan
                            </button>
                          </>
                        )}
                        {item.status === 'Diproses' && (
                          <button
                            type="button"
                            className="btn-action btn-finish"
                            onClick={() => updateOrderStatus(item.id, 'Selesai')}
                          >
                            Selesaikan
                          </button>
                        )}
                        <button
                          type="button"
                          className="btn-action btn-receipt"
                          onClick={() => setReceiptOrder(item)}
                        >
                          Lihat Struk
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* TAB 4: AKUN PELANGGAN (Poin 12: Reset password kasir) */}
          {activeTab === 'pelanggan' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Manajemen Akun Pelanggan</h3>
                <div className="search-box-wrap">
                  <input
                    type="text"
                    className="form-input"
                    value={searchUser}
                    onChange={(e) => setSearchUser(e.target.value)}
                    placeholder="Cari nama atau nomor HP..."
                  />
                </div>

                <div className="customer-acc-list">
                  {filteredCustomerAccounts.map((cust) => (
                    <div key={cust.id} className="customer-acc-item">
                      <div className="customer-acc-info">
                        <div className="customer-acc-name">
                          {cust.nama}{' '}
                          {cust.mustChangePassword && (
                            <span style={{ fontSize: 11, color: '#b67d0b', fontWeight: 600 }}>
                              (Wajib Ganti Password)
                            </span>
                          )}
                        </div>
                        <div className="customer-acc-sub">
                          {cust.nomorHp} · Terdaftar: {cust.registeredAt}
                        </div>
                      </div>
                      <button
                        type="button"
                        className="btn-reset-pwd"
                        onClick={() => handleTriggerReset(cust.nomorHp, cust.nama)}
                      >
                        Reset Password
                      </button>
                    </div>
                  ))}
                  {filteredCustomerAccounts.length === 0 && (
                    <div className="empty-notice">Tidak ada data pelanggan ditemukan.</div>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* TAB 5: STOK BAHAN BAKU DAPUR (Poin 13) */}
          {activeTab === 'stok' && (
            <div className="cashier-tab-content">
              <div className="section-card">
                <h3 className="section-title">Stok Bahan Baku Dapur</h3>
                <p className="status-flow-hint">
                  Stok otomatis berkurang saat pesanan dibuat dan otomatis kembali jika pesanan <b>Baru</b> dibatalkan.
                </p>
                <div className="customer-acc-list">
                  {bahanBaku.map((b) => (
                    <div key={b.id} className="customer-acc-item">
                      <div>
                        <div style={{ fontWeight: 600, fontSize: 14 }}>{b.nama}</div>
                        <div style={{ fontSize: 12, color: '#796e68' }}>Satuan: {b.satuan}</div>
                      </div>
                      <div style={{ fontWeight: 700, fontSize: 16, color: b.stok === 0 ? '#d33c30' : '#17834a' }}>
                        {b.stok} {b.satuan}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* POPUP MODAL RESET PASSWORD (Poin 12) */}
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

          {/* MODAL STRUK KASIR */}
          <ReceiptModal order={receiptOrder} onClose={() => setReceiptOrder(null)} />
        </div>
      </IonContent>
    </IonPage>
  );
};
