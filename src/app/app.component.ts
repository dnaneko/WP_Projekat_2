import { Component } from '@angular/core';
import { Router, RouterLink, RouterOutlet } from '@angular/router';
import { AuthService } from './services/auth.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, RouterLink, CommonModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css',
})
export class AppComponent {
  title = 'WP Projekat 2';

  isLoggedIn = false;

  constructor(private authService: AuthService, private router: Router) {
    // Subscribe to authentication state
    this.authService.isLoggedIn$.subscribe((status) => {
      this.isLoggedIn = status;
    });

    // Check login status on initialization
    this.authService.checkLoginStatus();
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/']);
  }
}
