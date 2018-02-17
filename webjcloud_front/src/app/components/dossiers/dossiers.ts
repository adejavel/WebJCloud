import { Component } from "@angular/core";
import {FolderService} from '../../services/folder.service';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
  templateUrl:'./dossiers.html'
})
export class Dossiers  {
  year;
  dossiers;
  last_year;
  loading=true;

  constructor(private folders :FolderService ,private route: ActivatedRoute,private router: Router){}
  ngOnInit() {
    this.dossiers=[];

    this.route.params.forEach((params: Params) => {
      let selectedId = +params['year'];
      this.year = selectedId;
    });
    this.update();

  }

  update(){
    this.loading=true;
    if (!this.valid()){
      return this.router.navigate(['/login']);
    }
    this.folders.getFoldersByYear(this.year).subscribe(
      (dossiers) => {
        this.dossiers= dossiers;
        this.loading=false;
      },

    )

  }

  valid(){
    var date = localStorage.getItem('valid');

    if (Date.now()>(parseInt(date)*1000)){
      return false;
    }
    return true;
  }

  delete(id){
    this.folders.deleteFile(id).subscribe(
      (res)=>{this.update()},
      (err)=>{console.log(err)}
    )
  }



}
