import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { MenuItem } from '../mockData';
import './CustomerMenuPage.css';

interface CustomerMenuPageProps {
  menus: MenuItem[];
  onOrderNow: (menu: MenuItem) => void;
  onLogout: () => void;
}

const CustomerMenuPage: React.FC<CustomerMenuPageProps> = ({ menus, onOrderNow, onLogout }) => {
  const [filter, setFilter] = useState<'semua' | 'minuman' | 'makanan'>('semua');

  const filteredMenus = menus.filter((item) => {
    if (filter === 'semua') return true;
    return item.kategori === filter;
  });

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper">
          <header className="page-header">
            <span className="page-title">Menu Kedai</span>
            <button type="button" className="header-logout-btn" onClick={onLogout}>
              Keluar
            </button>
          </header>

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
            {filteredMenus.map((item) => (
              <div
                key={item.id}
                className="menu-card"
                onClick={() => {
                  if (item.status === 'tersedia') {
                    onOrderNow(item);
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
                  <span className={`status-pill ${item.status === 'tersedia' ? 'badge-tersedia' : 'badge-habis'}`}>
                    {item.status === 'tersedia' ? 'Tersedia' : 'Habis'}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default CustomerMenuPage;

