import { Component } from "@angular/core";
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from "../../services/user.service";
import {AuthService} from '../../services/auth.service';
import {FolderService} from '../../services/folder.service';

@Component({
  templateUrl:'./header.html',
  styleUrls: ['./header.css'],
})
export class Header  {
  year="";
  dossier="";

  constructor(private route: ActivatedRoute,private router: Router,private _user:UserService){}
  ngOnInit() {

    this.route.params.forEach((params: Params) => {
      if (+params['year']){
        let selectedYear = +params['year'];
        this.year = selectedYear.toString();
      }
      if (+params['dossier']){
        let selectedDoss = +params['dossier'];
        this.dossier = selectedDoss.toString();
      }

    });
  }

  logout(){
    this._user.logout();
  }


}
