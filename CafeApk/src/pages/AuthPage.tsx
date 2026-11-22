import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import './AuthPage.css';

interface AuthPageProps {
  onLoginSuccess: (role: 'kasir' | 'pelanggan') => void;
}

const AuthPage: React.FC<AuthPageProps> = ({ onLoginSuccess }) => {
  const [tab, setTab] = useState<'masuk' | 'daftar'>('masuk');
  const [username, setUsername] = useState('081234567890');
  const [password, setPassword] = useState('12345678');
  const [nama, setNama] = useState('Rina Wulandari');
  const [phone, setPhone] = useState('081234567890');
  const [regPassword, setRegPassword] = useState('12345678');
  const [errorMsg, setErrorMsg] = useState('');

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (username.toLowerCase().includes('kasir')) {
      onLoginSuccess('kasir');
    } else {
      onLoginSuccess('pelanggan');
    }
  };

  const handleRegister = (e: React.FormEvent) => {
    e.preventDefault();
    if (phone.length < 10) {
      setErrorMsg('Nomor HP minimal 10 digit');
      return;
    }
    onLoginSuccess('pelanggan');
  };

  return (
    <IonPage>
      <IonContent fullscreen className="auth-content">
        <div className="auth-mobile-container">
          <header className="mobile-header">
            {tab === 'masuk' ? 'Kedai Kopi Senja' : 'Daftar Akun'}
          </header>

          <div className="auth-body">
            {/* Tab Switcher */}
            <div className="auth-segmented-control">
              <button
                type="button"
                className={`auth-segment-btn ${tab === 'masuk' ? 'active' : ''}`}
                onClick={() => { setTab('masuk'); setErrorMsg(''); }}
              >
                Masuk
              </button>
              <button
                type="button"
                className={`auth-segment-btn ${tab === 'daftar' ? 'active' : ''}`}
                onClick={() => { setTab('daftar'); setErrorMsg(''); }}
              >
                Daftar
              </button>
            </div>

            {tab === 'masuk' ? (
              <form onSubmit={handleLogin} className="auth-form">
                <div className="form-group">
                  <label className="form-label">Username / Nomor HP</label>
                  <input
                    type="text"
                    className="form-input"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    placeholder="Contoh: 081234567890 atau kasir01"
                    required
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Password</label>
                  <input
                    type="password"
                    className="form-input"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    required
                  />
                </div>

                {errorMsg && <div className="error-text">{errorMsg}</div>}

                <button type="submit" className="btn-coffee-primary" style={{ marginTop: 12 }}>
                  Masuk
                </button>

                <p className="auth-footer-hint">
                  Lupa password? Minta kasir meresetnya.
                </p>

                <div className="auth-demo-switcher">
                  <span className="demo-label">Mode Cepat Pengujian:</span>
                  <div className="demo-btns">
                    <button
                      type="button"
                      className="demo-pill"
                      onClick={() => onLoginSuccess('pelanggan')}
                    >
                      Login sbg Pelanggan
                    </button>
                    <button
                      type="button"
                      className="demo-pill"
                      onClick={() => onLoginSuccess('kasir')}
                    >
                      Login sbg Kasir
                    </button>
                  </div>
                </div>
              </form>
            ) : (
              <form onSubmit={handleRegister} className="auth-form">
                <div className="form-group">
                  <label className="form-label">Nama</label>
                  <input
                    type="text"
                    className="form-input"
                    value={nama}
                    onChange={(e) => setNama(e.target.value)}
                    placeholder="Rina Wulandari"
                    required
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Nomor HP</label>
                  <input
                    type="tel"
                    className="form-input"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="081234567890"
                    required
                  />
                  <span className="field-hint">Minimal 10 digit, tanpa huruf.</span>
                </div>

                <div className="form-group">
                  <label className="form-label">Password</label>
                  <input
                    type="password"
                    className="form-input"
                    value={regPassword}
                    onChange={(e) => setRegPassword(e.target.value)}
                    placeholder="••••••••"
                    required
                  />
                  <span className="field-hint">Minimal 8 karakter.</span>
                </div>

                {errorMsg && <div className="error-text">{errorMsg}</div>}

                <button type="submit" className="btn-coffee-primary" style={{ marginTop: 16 }}>
                  Daftar
                </button>

                <p className="auth-footer-hint">
                  Lupa password? Minta kasir meresetnya.
                </p>
              </form>
            )}
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default AuthPage;

