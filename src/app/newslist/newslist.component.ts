import { Component, OnInit } from '@angular/core';
import { NewsService } from '../services/news.service';
import { CommonModule } from '@angular/common';
import { FormsModule, NgForm } from '@angular/forms';

@Component({
  selector: 'app-newslist',
  imports: [CommonModule, FormsModule],
  templateUrl: './newslist.component.html',
  styleUrl: './newslist.component.css',
})
export class NewslistComponent implements OnInit {
  fromDate: string = '';
  toDate: string = '';
  news: any[] = [];
  categoryId: string = ''; // To store selected category

  constructor(private newsService: NewsService) {}

  ngOnInit(): void {
    this.fetchNews(); // Fetch news initially without any filters
  }

  // Method to handle category selection
  onCategorySelect(categoryId: string) {
    this.categoryId = categoryId;
    this.fetchNews(); // Fetch news immediately when category is selected
  }

  // Method to fetch news based on category and/or dates
  fetchNews() {
    this.newsService
      .getNews(this.fromDate, this.toDate, this.categoryId) // Pass all filters
      .subscribe(
        (data) => {
          this.news = data;
        },
        (error) => {
          console.error('Error fetching news', error);
        }
      );
  }

  // Handle form submission
  onSubmit() {
    this.fetchNews(); // Fetch news by dates and category
  }

  // Reset the form and fetch all news again
  onReset(form: NgForm) {
    form.resetForm();
    this.categoryId = ''; // Reset category selection
    this.fetchNews(); // Fetch all news after reset
  }
}
