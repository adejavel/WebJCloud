import { Component } from "@angular/core";
import { Router, ActivatedRoute, Params } from '@angular/router';
import {AuthService} from "../../services/auth.service";

@Component({
  templateUrl:'./valid.html'
})
export class Valid  {
  key;
  loading=false;
  done=false;
  firstname;

  constructor(private _auth : AuthService,private route: ActivatedRoute,private router: Router){}
  ngOnInit() {
    this.loading=true;

    this.route.params.forEach((params: Params) => {
      console.log(params);
      let key = params['key'];
      this.key = key.toString();
      console.log(this.key);
    });

    this._auth.get('/valid/user/'+(this.key)).subscribe(
      (response)=>{
        this.loading=false;
        this.done=true;
        this.firstname=response.name;
      },
    (error)=>{}
    )

  }

}
