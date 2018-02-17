import { Component } from "@angular/core";
import {ActivatedRoute,Router,Params} from "@angular/router";
import {DocService} from "../../services/doc.service";


const DOC_URL="https://files.webjcloud.fr/getfile.php?";
@Component({
  templateUrl:'./diaporama.html',
  styleUrls: ['./diaporama.css'],
})
export class Diaporama  {
  year;
  dossier;
  doc;
  documents;
  images=[];
  loading=true;
  link;
  image;
  next;
  prev;
  type;
  video
  comLoad;


  constructor(private route: ActivatedRoute,private router: Router,private _doc:DocService) {

  }

  ngAfterViewChecked(){

    if (this.type=='image'){
      this.image = document.getElementById('image');
      if (this.image!=null){
        this.setUrl(this.image)
      }
    }
    if (this.type =='video'){
      this.video = document.getElementById('video');
      if (this.video!=null){
        this.setUrl(this.video)
      }
    }

  }

  ngAfterViewInit(){

    //console.log(this.year);
    //this.getDocs()
  }
  ngOnInit(){
    this.comLoad=true;

    this.loading=true
    this.RefreshData()
    this._doc.getNextPrev(this.year,this.dossier,this.doc).subscribe(
      (res)=>{
        this.next = res.next;
        this.prev = res.prev;
        this.type = res.type;
        this.comLoad=false;
        this.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+this.doc+"&token="+this.getHeaders()+"&vign=0"
        this.loading=false;
      },
      (err)=>{console.log(err)}
    )
  }

  RefreshData(){
    this.route.params.forEach((params: Params) => {
      let selectedId = +params['year'];
      this.year = (selectedId.toString());
      this.dossier = params['dossier'];
      this.doc=params['doc'];
    });

  }

  getHeaders(customToken = null) {
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    return token
  }

  Back(){
    return this.router.navigate(['/'+this.year+'/'+this.dossier]);
  }

  Next(image:any,video:any){
    this.loading=true;
    this.doc = this.next;
    this.router.navigate(['/'+this.year+'/'+this.dossier+'/'+this.doc]);
    this.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+this.doc+"&token="+this.getHeaders()+"&vign=0"
    //this.setUrl(image)
    this.updateNextPrev()
    this.loading=false
  }

  Prev(image:any,video:any){
    this.loading=true;
    this.doc = this.prev;
    this.router.navigate(['/'+this.year+'/'+this.dossier+'/'+this.doc]);
    this.link = DOC_URL+'year='+this.year+'&dossier='+this.dossier+'&doc='+this.doc+"&token="+this.getHeaders()+"&vign=0"
    this.updateNextPrev()
    this.loading=false
    /*
    if (this.type=='image'){
      this.setUrl(image)
    }
    else if (this.type=='video'){
      this.setUrl(video)
    }*/

  }


  setUrl(image:any){
    image.src = this.link;
  }
  updateNextPrev(){
    this._doc.getNextPrev(this.year,this.dossier,this.doc).subscribe(
      (res)=>{
        this.next = res.next;
        this.prev = res.prev;
        this.type = res.type;
        //console.log(this.type)
      },
      (err)=>{console.log(err)}
    )

  }

  isLoaded(){
    this.loading=false;
  }



}
