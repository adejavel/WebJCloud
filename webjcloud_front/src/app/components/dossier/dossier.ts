import { Component } from "@angular/core";
import { Router, ActivatedRoute, Params } from '@angular/router';
import {DocService} from "../../services/doc.service";


const DOC_URL="https://files.webjcloud.fr/getfile.php?";
const DOWN_URL="https://files.webjcloud.fr/download.php?";
@Component({
  templateUrl:'./dossier.html',
  styleUrls: ['./dossier.css'],
})
export class Dossier  {
  year="";
  dossier="";
  loading=false;
  filesToUpload: Array<File> = [];
  address;
  documents;

  constructor(private route: ActivatedRoute,private router: Router,private _doc:DocService) {

  }
  ngOnInit() {
    this.route.params.forEach((params: Params) => {
      let selectedId = +params['year'];
      this.year = (selectedId.toString());
      this.dossier=params['dossier'];
    });
    this.updateDocs()
  }

  updateDocs(){
    this.loading=true;
    this._doc.getDocumentsByFolder(this.dossier,this.year).subscribe(
      (res)=>{
        this.loading=false;
        this.documents=res;
        this.documents.forEach((doc)=>{
          doc.isreadable=false;
          doc.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+doc.url+"&token="+this.getHeaders()+"&vign=1"
          doc.linkDown = DOWN_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+doc.url+"&token="+this.getHeaders()
          if ((doc.mime=="image")){
            doc.isimage=true;
          }
          if (doc.mime=="application"&&doc.truetype=="pdf"){
            doc.ispdf=true;
            doc.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+doc.url+"&token="+this.getHeaders()+"&vign=0"
          }
          if (doc.mime=="video"){
            doc.isvideo=true;
            doc.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+doc.url+"&token="+this.getHeaders()+"&vign=0"
          }
        })
      },
      (err)=>{
        console.log(err)
      }
    )
  }
  getHeaders(customToken = null) {
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    return token
  }

  delete(id){
    this._doc.deleteFile(id).subscribe(
      (res)=>{
        this.updateDocs();
      },
      (err)=>{console.log(err)}
    )
  }


}
