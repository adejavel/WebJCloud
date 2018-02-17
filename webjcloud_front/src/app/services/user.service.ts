import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Rx';
import { AuthService } from './auth.service';

@Injectable()
export class UserService {
  public loggedIn = false;
  public roles;

  public user = {
    comments: [
      { name: 'CDP 1', stars: 3, comment: 'text' }
    ]
  }

  constructor(
    public router: Router,
    public _auth: AuthService
  ) {
    this.loggedIn = !!localStorage.getItem('auth_token');

    this._auth.errorHandled.subscribe(err => {
      if (err.status === 403) {
        this.logout();
        this.router.navigate(['/login']);
      }
    })
  }

  login(username, password) {
    let headers = this._auth.getHeaders();
    let body = { login:username, password:password };
    return this._auth.http.post(this._auth.url + '/auth-tokens', body, headers);
  }

  logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('valid');
    this.router.navigate(['/login']);
    this.loggedIn = false;
  }

  signin(data){
    let headers = this._auth.getHeaders();
    return this._auth.http.post(this._auth.url + '/users', data, headers);
  }

  isLoggedIn() {
    if (!this.loggedIn){return false}
    var tt=Math.floor(Date.now() / 1000)
    var valid = parseInt(localStorage.getItem('valid'));
    var diff = (tt-valid)
    if (diff<0){
      return true;
    }
    return false;
  }


}
