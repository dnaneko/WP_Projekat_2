import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class NewsService {
  private apiUrl = 'http://localhost/backend/api/news.php';

  constructor(private http: HttpClient) {}

  // Accepts from, to, and categoryId separately
  getNews(from?: string, to?: string, categoryId?: string): Observable<any> {
    let params = new HttpParams();

    // Append the 'from' and 'to' parameters if provided
    if (from) {
      params = params.set('from', from);
    }

    if (to) {
      params = params.set('to', to);
    }

    // Append the 'categoryId' parameter if provided
    if (categoryId) {
      params = params.set('category_id', categoryId);
    }

    // Send the GET request with the query parameters
    return this.http.get<any>(this.apiUrl, { params });
  }
}
