import { Component,Input,EventEmitter,Output } from '@angular/core';
import { FileUploader,Headers } from 'ng2-file-upload';

declare var $: any;

const URL = 'https://files.webjcloud.fr/upload.php';

@Component({
  selector: 'uploader',
  templateUrl:'./uploader.html',
  styleUrls: ['./uploader.css'],
})

export class Uploader {
  @Input() dossier;
  @Input() year;
  @Output() refresh = new EventEmitter();
  public uploader = new FileUploader({url: URL,headers: []});

  public hasBaseDropZoneOver:boolean = false;
  public hasAnotherDropZoneOver:boolean = false;
  constructor() {
  }
  ngAfterViewInit() {
    this.uploader.onAfterAddingFile = (item => {
      item.withCredentials = false;
    });
    this.uploader.onBuildItemForm=(item,form)=>{
      form.append('token',this.getHeaders());
      form.append('dossier',this.dossier);
      form.append('year',this.year);
    }
  }

  public fileOverBase(e:any):void {
    this.hasBaseDropZoneOver = e;
  }

  public fileOverAnother(e:any):void {
    this.hasAnotherDropZoneOver = e;
  }

  showModal(){
    $('#FolderModal').modal('show');
  }
  closeModal(){
    $('#FolderModal').modal('hide');
  }

  Close(){
    this.closeModal()
    this.RefreshAction()
  }

  getHeaders(customToken = null) {
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    return token
  }
  RefreshAction(){
    this.refresh.emit(this);
  }
}
