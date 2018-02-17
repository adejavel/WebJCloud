import { Injectable, Output, EventEmitter } from '@angular/core';
import { Http, Headers, Response } from '@angular/http';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Rx';

// this is just a dumb duplicate of secured abstract service
//
// TODO make this a ProfileService and implement profile specific method (can also be merged into UserService)
@Injectable()
export class AuthService {
  public url = "BACKEND_URL";
  public file_url = "FILES_APPLICATION_URL";
  @Output() errorHandled = new EventEmitter();

  constructor(
    public http: Http,
    private router: Router
  ) { }

  get(url: string) {

    return this.http.get(this.url + url, this.getHeaders())
      .map(data => this.extractData(data))
      .catch(error => this.handleError(error));
  }

  post(url: string, body) {

      return this.http.post(this.url + url, body, this.getHeaders())
        .map(data => this.extractData(data))
        .catch((error) => this.handleError(error));

  }

  put(url: string, body) {
    return this.http.put(this.url + url, body, this.getHeaders())
      .map(data => this.extractData(data))
      .catch(error => this.handleError(error));
  }

  delete(url: string) {
    return this.http.delete(this.url + url, this.getHeaders())
      .map(data => this.extractData(data))
      .catch(error => this.handleError(error));
  }

  upload(url,formData){
    return this.http.post(this.url + url, formData,this.getHeaders())
      .map(data => this.extractData(data))
      .catch(error => this.handleError(error));
  }

  deleteFiles(url){
    return this.http.delete(this.file_url + url)
      .map(data => this.extractData(data))
      .catch(error => this.handleError(error));
  }



  getHeaders(customToken = null) {
    let headers = new Headers();
    headers.append('Content-Type', 'application/json');
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    headers.append('X-Auth-Token', token);
    let timeout = 5000;
    return { headers, timeout };
  }

  extractData(res: Response) {
    let body;
    if (res.text()) {
      body = res.json();
    }
    return body || {};
  }

  updateToken(headers) {
    let refreshToken = headers._headersMap.get('refresh')
    if (refreshToken) {
      console.log('token need a refresh', refreshToken[0])
    }
  }

  handleError(error: Response) {
    this.errorHandled.emit(error);
    console.error(error);
    return Observable.throw(error.json());
  }


}
