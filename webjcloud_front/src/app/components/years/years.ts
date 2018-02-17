import { Component } from "@angular/core";
import {FolderService} from '../../services/folder.service';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
  templateUrl:'./years.html'
})
export class Years  {
  dossiers;
  years = [];
  loading=true;

  constructor(private folders :FolderService ,private router : Router){}
  ngOnInit() {
    this.updateData();
  }

  updateData(){

    if (!this.valid()){
      return this.router.navigate(['/login']);
    }
    this.loading=true;
    this.folders.getFolders().subscribe(
      (dossiers) => {
        dossiers.forEach((dossier)=>{
            if (this.years.indexOf(dossier.year.toString())===-1){
              this.years.push(dossier.year.toString())
            }
          }

        )
        this.loading=false;
      }
    )
  }
  valid(){
    var date = localStorage.getItem('valid');

    if (Date.now()>(parseInt(date)*1000)){
      return false;
    }
    return true;
  }

}
