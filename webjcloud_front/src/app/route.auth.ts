import { Injectable } from "@angular/core";
import { Router, CanActivate, ActivatedRouteSnapshot, CanActivateChild } from "@angular/router";
import {Observable} from "rxjs/Rx";
import { UserService } from "./services/user.service";

@Injectable()
export class AuthRoute implements CanActivateChild {
  constructor(private _service: UserService, private route: Router) {}

  canActivateChild() {
    if (this._service.isLoggedIn()) {
      return true;
    }
    this.route.navigate(['/login']);
    return false;
  }
  canActivate() {
    if (this._service.isLoggedIn()) {
      return true;
    }
    this.route.navigate(['/login']);
    return false;
  }

}

