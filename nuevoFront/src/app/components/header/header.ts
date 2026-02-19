import { Component, EventEmitter, Output, inject, signal, OnInit, OnDestroy } from '@angular/core';
import { DatePipe } from '@angular/common';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatMenuModule } from '@angular/material/menu';
import { Router, RouterLink } from '@angular/router';
import { UserService } from '../../services/user-service';
import { AuthService } from '../../services/auth-service';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [MatToolbarModule, MatButtonModule, MatIconModule, MatMenuModule, RouterLink, DatePipe],
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header implements OnInit, OnDestroy {
  @Output() toggleSidenav = new EventEmitter<void>();
  private userService = inject(UserService);
  private authService = inject(AuthService); // Inject AuthService
  private router = inject(Router); // Inject Router
  user = signal<any>(null);
  role = this.authService.role;

  // Señal para la hora actual
  currentTime = signal(new Date());
  private intervalId: any;

  async ngOnInit() {
    // Info usuario
    try {
      console.log('Header Initialized - v3');
      const userData = await firstValueFrom(this.userService.userInfo());
      console.log('Header received user info:', userData);
      this.user.set(userData);
    } catch (error: any) {
      console.error('Header UserInfo error:', error);
      if (error.status === 401) {
        // Absolutely NO redirect here
        console.warn('UserInfo 401 handled. User might be logged out, but NOT redirecting.');
      }
    }

    // Actualizar reloj cada segundo
    this.intervalId = setInterval(() => {
      this.currentTime.set(new Date());
    }, 1000);
  }

  ngOnDestroy() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  }

  onToggle() {
    this.toggleSidenav.emit();
  }

  logout() {
    this.authService.logOut().subscribe({
      next: () => this.router.navigate(['/login']),
      error: (err) => {
        console.error('Logout failed', err);
        // Even if the backend call fails, we should probably redirect to login
        this.router.navigate(['/login']);
      },
    });
  }
}
