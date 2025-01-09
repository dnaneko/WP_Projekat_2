import { Component } from '@angular/core';

import { HeroComponent } from '../hero/hero.component';
import { NewslistComponent } from '../newslist/newslist.component';

@Component({
  selector: 'app-home',
  imports: [HeroComponent, NewslistComponent],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css',
})
export class HomeComponent {}
