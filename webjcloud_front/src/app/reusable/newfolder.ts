import { Component, Input, Output, EventEmitter, SimpleChange } from '@angular/core'
import {FolderService} from '../services/folder.service';
import { Router, ActivatedRoute, Params } from '@angular/router';

declare var $: any;

@Component({
  selector: 'new-folder',
  template: `
    <div class="" data-target="#FolderModal" (click)="showModal()">
      <div class="card text-center add" >
        <div class="card-body">
          <i class="material-icons" style="font-size: 150px">create_new_folder</i>
          <h4 class="card-title" >Nouveau dossier</h4>
        </div>
      </div>
    </div>
    <div class="modal fade" id="FolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nouveau dossier</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <div class="alert alert-danger" role="alert" *ngIf="error!==''" style='font-size: 20px'>
            {{error}}
          </div>
            <form #Folder="ngForm">
              <div class="form-group">
                <label for="annee" style='font-size: 15px'>Année : </label>
                <select class="form-control" id="annee" [(ngModel)]="year" name="annee" required>
                  <option>2018</option>
                  <option>2017</option>
                  <option>2016</option>
                  <option>2015</option>
                </select>
              </div>
              <div class="form-group">
                <label for="titre" style='font-size: 20px'>Titre :</label>
                <input type="text" class="form-control" id="titre" name="titre" [(ngModel)]="name" placeholder="Titre" required>
              </div>
              <div class="form-group">
                <label for="description" style='font-size: 20px'>Description</label>
                <textarea class="form-control" id="description" rows="3" [(ngModel)]="description" name="description"></textarea>
              </div>
              
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary" [disabled]="!Folder.form.valid" (click)="newFolder()">Créer</button>
          </div>
          
        </div>
      </div>
    </div>
  `,
  styles:[
      `
      


    `
  ]
})
export class NewFolder {
  show=true;
  year;
  name;
  description="";
  error ="";
  @Output() refresh = new EventEmitter();

  constructor(private _folder:FolderService,private route: ActivatedRoute,private router: Router ) {

  }
  ngOnInit() {
    this.route.params.forEach((params: Params) => {
      let selectedId = +params['year'];
      this.year = parseInt(selectedId.toString());
    });
  }

  showModal(){
    $('#FolderModal').modal('show');
  }
  closeModal(){
    $('#FolderModal').modal('hide');
  }

  newFolder(){
    if (!this.valid()){
      this.closeModal();
      return this.router.navigate(['/login']);
    }
    let data ={
      name:this.name,
      description:this.description,
      year:parseInt(this.year)
    }
    this._folder.newFolder(data).subscribe(
      res => {
        this.closeModal();
        this.RefreshAction();
      },
      (err)=>{
        if (err.message==="Folder already existing"){
          this.error ="Un dossier avec  un nom similaire existe déjà !";
        }
      }
    );

  }
  RefreshAction(){
    this.refresh.emit(this);
  }
  valid(){
    var date = localStorage.getItem('valid');

    if (Date.now()>(parseInt(date)*1000)){
      return false;
    }
    return true;
  }
}
