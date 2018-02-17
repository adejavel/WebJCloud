import { Injectable, Output, EventEmitter } from '@angular/core';
import { AuthService } from "./auth.service";

@Injectable()
export class FolderService {
  constructor(private _auth:AuthService){}

  getFolders(){
    return this._auth.get('/folders');
  }
  getFoldersByYear(year){
    return this._auth.get('/folders/year/'+year);
  }
  newFolder(data){
    return this._auth.post('/folders',data);
  }
  deleteFile(id){
    return this._auth.deleteFiles('/delete.php?token='+this.getHeaders()+'&id='+id+'&doc=0');
  }

  getHeaders(customToken = null) {
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    return token
  }
}

