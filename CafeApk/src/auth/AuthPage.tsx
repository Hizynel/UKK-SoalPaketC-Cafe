import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { AppHeader } from '../components/Header';
import './AuthPage.css';

interface AuthPageProps {
  onNeedForceChangePassword: () => void;
}

export const AuthPage: React.FC<AuthPageProps> = ({ onNeedForceChangePassword }) => {
  const { login, register } = useCafe();
  const [tab, setTab] = useState<'masuk' | 'daftar'>('masuk');

  // Login form
  const [loginHp, setLoginHp] = useState('');
  const [loginPass, setLoginPass] = useState('');

  // Register form (Poin 12: Nomor HP unik + password min 8 karakter)
  const [regNama, setRegNama] = useState('');
  const [regHp, setRegHp] = useState('');
  const [regPass, setRegPass] = useState('');

  const [errorMsg, setErrorMsg] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg('');
    setSuccessMsg('');

    const res = await login(loginHp, loginPass);
    if (!res.success) {
      setErrorMsg(res.message || 'Login gagal.');
    } else if (res.mustChange) {
      onNeedForceChangePassword();
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg('');
    setSuccessMsg('');

    const res = await register(regNama, regHp, regPass);
    if (!res.success) {
      setErrorMsg(res.message || 'Pendaftaran gagal.');
    } else {
      setSuccessMsg('Akun berhasil dibuat! Silakan masuk.');
      setTab('masuk');
      setLoginHp(regHp);
      setLoginPass(regPass);
      setRegNama('');
      setRegHp('');
      setRegPass('');
    }
  };

  return (
    <IonPage>
      <IonContent fullscreen className="auth-content">
        <div className="auth-mobile-container">
          <AppHeader title={tab === 'masuk' ? 'Kedai Kopi Senja' : 'Daftar Akun'} />

          <div className="auth-body">
            {/* Tab Switcher */}
            <div className="auth-segmented-control">
              <button
                type="button"
                className={`auth-segment-btn ${tab === 'masuk' ? 'active' : ''}`}
                onClick={() => {
                  setTab('masuk');
                  setErrorMsg('');
                  setSuccessMsg('');
                }}
              >
                Masuk
              </button>
              <button
                type="button"
                className={`auth-segment-btn ${tab === 'daftar' ? 'active' : ''}`}
                onClick={() => {
                  setTab('daftar');
                  setErrorMsg('');
                  setSuccessMsg('');
                }}
              >
                Daftar
              </button>
            </div>

            {successMsg && <div className="success-banner">{successMsg}</div>}
            {errorMsg && <div className="error-text">{errorMsg}</div>}

            {/* TAB MASUK */}
            {tab === 'masuk' ? (
              <form onSubmit={handleLogin} className="auth-form">
                <div className="form-group">
                  <label className="form-label">Nomor HP / Username</label>
                  <input
                    type="text"
                    className="form-input"
                    value={loginHp}
                    onChange={(e) => setLoginHp(e.target.value)}
                    placeholder="Contoh: 081234567890 atau kasir01"
                    required
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Password</label>
                  <input
                    type="password"
                    className="form-input"
                    value={loginPass}
                    onChange={(e) => setLoginPass(e.target.value)}
                    placeholder="Minimal 8 karakter"
                    required
                  />
                </div>

                <button type="submit" className="btn-coffee-primary" style={{ marginTop: 12 }}>
                  Masuk
                </button>

                <p className="auth-footer-hint">
                  Lupa password? Minta kasir meresetnya.
                </p>
              </form>
            ) : (
              /* TAB DAFTAR */
              <form onSubmit={handleRegister} className="auth-form">
                <div className="form-group">
                  <label className="form-label">Nama</label>
                  <input
                    type="text"
                    className="form-input"
                    value={regNama}
                    onChange={(e) => setRegNama(e.target.value)}
                    placeholder="Nama Lengkap"
                    required
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Nomor HP</label>
                  <input
                    type="tel"
                    className="form-input"
                    value={regHp}
                    onChange={(e) => setRegHp(e.target.value)}
                    placeholder="08xxxxxxxxxx"
                    required
                  />
                  <span className="field-hint">Minimal 10 digit, tanpa huruf.</span>
                </div>

                <div className="form-group">
                  <label className="form-label">Password</label>
                  <input
                    type="password"
                    className="form-input"
                    value={regPass}
                    onChange={(e) => setRegPass(e.target.value)}
                    placeholder="••••••••"
                    required
                  />
                  <span className="field-hint">Minimal 8 karakter.</span>
                </div>

                <button type="submit" className="btn-coffee-primary" style={{ marginTop: 12 }}>
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

