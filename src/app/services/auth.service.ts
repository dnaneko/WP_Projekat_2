import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private API_LOGIN_URL = 'http://localhost/backend/api/login.php';
  private API_REGISTER_URL = 'http://localhost/backend/api/register.php';
  private API_CP_URL = 'http://localhost/backend/api/change-password.php';

  private isLoggedInSubject = new BehaviorSubject<boolean>(false);
  isLoggedIn$ = this.isLoggedInSubject.asObservable();

  constructor(private http: HttpClient) {}

  register(data: any) {
    return this.http.post(`${this.API_REGISTER_URL}`, {
      ...data,
      action: 'register',
    });
  }

  login(data: any) {
    return this.http.post(`${this.API_LOGIN_URL}`, {
      ...data,
      action: 'login',
    });
  }

  handleLoginResponse(response: any) {
    if (response.success && response.token) {
      localStorage.setItem('auth_token', response.token);
      this.isLoggedInSubject.next(true); // Update login state
    }
  }

  changePassword(data: any) {
    return this.http.post(`${this.API_CP_URL}`, {
      ...data,
      action: 'change_password',
    });
  }

  checkLoginStatus() {
    const token = localStorage.getItem('auth_token');
    this.isLoggedInSubject.next(!!token); // Check if token exists
  }

  logout() {
    localStorage.removeItem('auth_token');
    this.isLoggedInSubject.next(false); // Update login state
  }
}
