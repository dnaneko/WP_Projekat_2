import { Component } from '@angular/core';
import {
  FormGroup,
  FormControl,
  Validators,
  AbstractControl,
  ValidationErrors,
} from '@angular/forms';
import { ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent {
  registerForm: FormGroup;
  successMessage = '';
  errorMessage = '';
  isSubmitting = false;

  constructor(private authService: AuthService, private router: Router) {
    this.registerForm = new FormGroup(
      {
        name: new FormControl('', [Validators.required]),
        lastName: new FormControl('', [Validators.required]),
        username: new FormControl('', [
          Validators.required,
          Validators.minLength(4),
        ]),
        email: new FormControl('', [Validators.required, Validators.email]),
        password: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
        passwordConfirm: new FormControl('', [Validators.required]),
      },
      { validators: this.passwordMatchValidator }
    );
  }

  // Custom Validator
  passwordMatchValidator(control: AbstractControl): ValidationErrors | null {
    const password = control.get('password')?.value;
    const passwordConfirm = control.get('passwordConfirm')?.value;
    return password === passwordConfirm ? null : { passwordMismatch: true };
  }

  onSubmit() {
    if (this.registerForm.valid) {
      this.isSubmitting = true;
      this.successMessage = '';
      this.errorMessage = '';

      const formData = { ...this.registerForm.value };
      delete formData.passwordConfirm;

      this.authService.register(formData).subscribe({
        next: (response: any) => {
          if (response.success) {
            this.successMessage = response.message;
            this.registerForm.reset();
          } else {
            this.errorMessage = response.message;
          }
          this.isSubmitting = false;
          this.router.navigate(['/']);
        },
        error: (error) => {
          this.errorMessage =
            'An error occurred during registration. Please try again.';
          this.isSubmitting = false;
        },
      });
    } else {
      this.errorMessage = 'Please fill out the form correctly.';
    }
  }

  // Getter methods for easy access to form controls
  get name() {
    return this.registerForm.get('name');
  }
  get lastName() {
    return this.registerForm.get('lastName');
  }
  get username() {
    return this.registerForm.get('username');
  }
  get email() {
    return this.registerForm.get('email');
  }
  get password() {
    return this.registerForm.get('password');
  }
  get passwordConfirm() {
    return this.registerForm.get('passwordConfirm');
  }
  get passwordMismatch() {
    return this.registerForm.errors?.['passwordMismatch'];
  }
}
