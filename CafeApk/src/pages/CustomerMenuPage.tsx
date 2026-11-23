import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { AppHeader } from '../components/Header';
import { StatusBadge } from '../components/StatusBadge';
import './CustomerMenuPage.css';

interface CustomerMenuPageProps {
  onSelectMenuToOrder: (menuId: number) => void;
  onOpenChangePassword: () => void;
}

export const CustomerMenuPage: React.FC<CustomerMenuPageProps> = ({
  onSelectMenuToOrder,
  onOpenChangePassword,
}) => {
  const { menus, isMenuAvailable, currentUser, logout } = useCafe();
  const [filter, setFilter] = useState<'semua' | 'minuman' | 'makanan'>('semua');

  const filteredMenus = menus.filter((item) => {
    if (filter === 'semua') return true;
    return item.kategori === filter;
  });

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <AppHeader
            title="Menu Kedai"
            subtitle={`Halo, ${currentUser?.nama || 'Pelanggan'}`}
            rightAction={
              <div style={{ display: 'flex', gap: 6 }}>
                <button
                  type="button"
                  className="header-action-btn"
                  onClick={onOpenChangePassword}
                  title="Ganti Password"
                >
                  Kunci
                </button>
                <button
                  type="button"
                  className="header-action-btn"
                  onClick={logout}
                >
                  Keluar
                </button>
              </div>
            }
          />

          {/* Filter Pills */}
          <div className="filter-pills-row">
            <button
              className={`pill-button ${filter === 'semua' ? 'active' : ''}`}
              onClick={() => setFilter('semua')}
            >
              Semua
            </button>
            <button
              className={`pill-button ${filter === 'minuman' ? 'active' : ''}`}
              onClick={() => setFilter('minuman')}
            >
              Minuman
            </button>
            <button
              className={`pill-button ${filter === 'makanan' ? 'active' : ''}`}
              onClick={() => setFilter('makanan')}
            >
              Makanan
            </button>
          </div>

          {/* Menu List */}
          <div className="menu-list-container">
            {filteredMenus.map((item) => {
              const available = isMenuAvailable(item.id);
              return (
                <div
                  key={item.id}
                  className={`menu-card ${!available ? 'menu-card-disabled' : ''}`}
                  onClick={() => {
                    if (available) {
                      onSelectMenuToOrder(item.id);
                    } else {
                      alert(`Maaf, ${item.nama} sedang habis karena stok bahan di dapur tidak mencukupi.`);
                    }
                  }}
                >
                  <div className="menu-thumb-placeholder">
                    <span className="thumb-icon">{item.kategori === 'minuman' ? '☕' : '🍽️'}</span>
                  </div>
                  <div className="menu-info">
                    <div className="menu-name">{item.nama}</div>
                    <div className="menu-price">Rp {item.harga.toLocaleString('id-ID')}</div>
                  </div>
                  <div>
                    <StatusBadge status={available ? 'Tersedia' : 'Habis'} />
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};
