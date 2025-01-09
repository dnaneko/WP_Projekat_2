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

@Component({
  selector: 'app-change-password',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './change-password.component.html',
  styleUrl: './change-password.component.css',
})
export class ChangePasswordComponent {
  changePasswordForm: FormGroup;

  constructor() {
    this.changePasswordForm = new FormGroup(
      {
        username: new FormControl('', [Validators.required]),
        currentPassword: new FormControl('', [Validators.required]),
        newPassword: new FormControl('', [
          Validators.required,
          Validators.minLength(6),
        ]),
      },
      { validators: this.passwordsNotSameValidator }
    );
  }

  // Custom Validator: Ensures new password isn't the same as the current one
  passwordsNotSameValidator(control: AbstractControl): ValidationErrors | null {
    const currentPassword = control.get('currentPassword')?.value;
    const newPassword = control.get('newPassword')?.value;
    return currentPassword !== newPassword ? null : { passwordSameError: true };
  }

  onSubmit() {
    if (this.changePasswordForm.valid) {
      console.log('Form submitted', this.changePasswordForm.value);
    } else {
      console.log('Form is invalid');
    }
  }

  // Getters for form controls
  get username() {
    return this.changePasswordForm.get('username');
  }
  get currentPassword() {
    return this.changePasswordForm.get('currentPassword');
  }
  get newPassword() {
    return this.changePasswordForm.get('newPassword');
  }
  get passwordSameError() {
    return this.changePasswordForm.errors?.['passwordSameError'];
  }
}
