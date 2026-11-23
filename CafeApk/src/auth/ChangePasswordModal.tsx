import React, { useState } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useCafe } from '../context/CafeContext';
import { AppHeader } from '../components/Header';
import './AuthPage.css';

interface ChangePasswordModalProps {
  forced?: boolean;
  onSuccess: () => void;
  onCancel?: () => void;
}

export const ChangePasswordModal: React.FC<ChangePasswordModalProps> = ({
  forced = false,
  onSuccess,
  onCancel,
}) => {
  const { currentUser, changePassword, forceChangePassword, logout } = useCafe();
  const [oldPassword, setOldPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [errorMsg, setErrorMsg] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg('');

    if (newPassword.length < 8) {
      setErrorMsg('Password baru minimal 8 karakter.');
      return;
    }
    if (newPassword !== confirmPassword) {
      setErrorMsg('Konfirmasi password tidak cocok.');
      return;
    }

    if (forced) {
      const res = await forceChangePassword(newPassword);
      if (!res.success) {
        setErrorMsg(res.message || 'Gagal mengubah password.');
      } else {
        alert('Password berhasil diperbarui! Selamat datang kembali.');
        onSuccess();
      }
    } else {
      const res = await changePassword(oldPassword, newPassword);
      if (!res.success) {
        setErrorMsg(res.message || 'Gagal mengubah password.');
      } else {
        alert('Password berhasil diubah.');
        onSuccess();
      }
    }
  };

  return (
    <IonPage>
      <IonContent fullscreen>
        <div className="auth-mobile-container">
          <AppHeader
            title={forced ? 'Wajib Ganti Password' : 'Ganti Password'}
            rightAction={
              forced ? (
                <button
                  type="button"
                  className="btn-coffee-outline"
                  style={{ color: '#fff', borderColor: '#fff' }}
                  onClick={logout}
                >
                  Batal & Keluar
                </button>
              ) : (
                onCancel && (
                  <button
                    type="button"
                    className="btn-coffee-outline"
                    style={{ color: '#fff', borderColor: '#fff' }}
                    onClick={onCancel}
                  >
                    Kembali
                  </button>
                )
              )
            }
          />

          <div className="auth-body">
            {forced ? (
              <div className="alert-notice-box">
                <strong>Perhatian:</strong> Password Anda telah direset oleh kasir. Demi keamanan, Anda
                diwajibkan membuat password baru minimal 8 karakter sebelum melanjutkan.
              </div>
            ) : (
              <p className="auth-subtitle-text">
                Ubah password akun Anda ({currentUser?.nama} - {currentUser?.nomorHp}).
              </p>
            )}

            {errorMsg && <div className="error-text">{errorMsg}</div>}

            <form onSubmit={handleSubmit} className="auth-form" style={{ marginTop: 16 }}>
              {!forced && (
                <div className="form-group">
                  <label className="form-label">Password Lama</label>
                  <input
                    type="password"
                    className="form-input"
                    value={oldPassword}
                    onChange={(e) => setOldPassword(e.target.value)}
                    placeholder="Masukkan password saat ini"
                    required
                  />
                </div>
              )}

              <div className="form-group">
                <label className="form-label">Password Baru</label>
                <input
                  type="password"
                  className="form-input"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  placeholder="Minimal 8 karakter"
                  required
                />
                <span className="field-hint">Minimal 8 karakter.</span>
              </div>

              <div className="form-group">
                <label className="form-label">Ulangi Password Baru</label>
                <input
                  type="password"
                  className="form-input"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  placeholder="Ulangi password baru"
                  required
                />
              </div>

              <button type="submit" className="btn-coffee-primary" style={{ marginTop: 14 }}>
                Simpan Password Baru
              </button>
            </form>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

