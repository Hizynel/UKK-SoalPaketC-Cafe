import React from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { Order } from '../types';
import './OrderSuccessPage.css';

interface OrderSuccessPageProps {
  order: Order;
  onBack: () => void;
}

export const OrderSuccessPage: React.FC<OrderSuccessPageProps> = ({ order, onBack }) => {
  const letters = order.kode.split('');

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="mobile-view-wrapper success-wrapper">
          <div className="success-content">
            {/* Green Check Circle */}
            <div className="check-circle">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1d824d" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                <polyline points="20 6 9 17 4 12" />
              </svg>
            </div>

            <h1 className="success-title">Pesanan Berhasil Dibuat</h1>
            <p className="success-desc">
              Tunjukkan kode unik ini ke kasir untuk verifikasi pesanan Anda.
            </p>

            {/* Code Boxes */}
            <div className="code-boxes-container">
              {letters.map((char, idx) => (
                <div key={idx} className="code-box">
                  {char}
                </div>
              ))}
            </div>

            {/* Order Card details */}
            <div className="order-details-card">
              <div className="details-row">
                <span className="details-label">Meja</span>
                <span className="details-val">{order.meja.replace('Meja ', '')}</span>
              </div>
              <div className="details-row">
                <span className="details-label">Pembayaran</span>
                <span className="details-val">{order.metodePembayaran}</span>
              </div>
              <div className="details-row">
                <span className="details-label">Total</span>
                <span className="details-val font-bold">
                  Rp {order.total.toLocaleString('id-ID')}
                </span>
              </div>
            </div>
          </div>

          <div className="success-footer">
            <button type="button" className="btn-coffee-primary" onClick={onBack}>
              Kembali ke Riwayat
            </button>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};
