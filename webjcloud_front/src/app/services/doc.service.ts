import { Injectable, Output, EventEmitter } from '@angular/core';
import { AuthService } from "./auth.service";

@Injectable()
export class DocService {
  constructor(private _auth:AuthService){}

  getDocumentsByFolder(folder,year){
    return this._auth.get('/documents/folder/'+year+'/'+folder);
  }
  getNextPrev(year,folder,doc){
    return this._auth.get('/documents/next/'+year+'/'+folder+'/'+doc);
  }
  deleteFile(id){
    return this._auth.deleteFiles('/delete.php?token='+this.getHeaders()+'&id='+id+'&doc=1');
  }

  getHeaders(customToken = null) {
    let token = customToken ? customToken : localStorage.getItem('auth_token');
    return token
  }

}
