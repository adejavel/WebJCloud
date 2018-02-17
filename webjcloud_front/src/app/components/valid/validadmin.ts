import { Component } from "@angular/core";
import { Router, ActivatedRoute, Params } from '@angular/router';
import {AuthService} from "../../services/auth.service";

@Component({
  templateUrl:'./validadmin.html'
})
export class ValidAdmin   {
  key;
  loading=false;
  done=false;
  firstname;
  lastname;
  email;
  tovalid=true;

  constructor(private _auth : AuthService,private route: ActivatedRoute,private router: Router){}
  ngOnInit() {
    this.loading=true;

    this.route.params.forEach((params: Params) => {
      console.log(params);
      let key = params['key'];
      this.key = key.toString();
      console.log(this.key);
    });

    this._auth.get('/valid/admin/info/'+(this.key)).subscribe(
      (response)=>{
        this.loading=false;
        this.firstname=response.firstname;
        this.lastname=response.lastname;
        this.email = response.email;
      },
      (error)=>{}
    )

  }

  validUser(){
    this.tovalid=false;
    this.loading=true;
    this._auth.get('/valid/admin/valid/'+(this.key)).subscribe(
      (response)=>{
        this.loading=false;
      },
      (error)=>{}
    )
  }

}
