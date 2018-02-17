import { BrowserModule } from '@angular/platform-browser';
import { NgModule,CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HttpModule }    from '@angular/http';
import { FormsModule }   from '@angular/forms';
import { FileSelectDirective } from 'ng2-file-upload';

import { AppComponent } from './app.component';

import { AuthRoute } from "./route.auth";
import { routing } from './app.routes';

//Components
import {Login } from './components/login/login';
import {Dossiers} from './components/dossiers/dossiers';
import {Header} from'./components/header/header';
import {Years} from './components/years/years';
import {Dossier} from './components/dossier/dossier';
import { Valid} from './components/valid/valid';
import {Diaporama} from "./components/diaporama/diaporama";
import { ValidAdmin} from './components/valid/validadmin'

//Reusable
import {Loader} from './reusable/loader';
import {NewFolder} from './reusable/newfolder';
import {Uploader} from "./reusable/uploader/uploader";
import {Infos} from "./reusable/infos/infos";


//Services :
import {UserService} from './services/user.service';
import {AuthService} from './services/auth.service';
import {FolderService} from './services/folder.service';
import {DocService} from "./services/doc.service";

@NgModule({
  declarations: [
    AppComponent,
    Login,
    Dossiers,
    Header,
    Loader,
    Years,
    NewFolder,
    Dossier,
    Valid,
    Uploader,
    FileSelectDirective,
    Diaporama,
    Infos,
    ValidAdmin,
  ],
  imports: [
    BrowserModule,
    HttpModule,
    FormsModule,
    routing,
  ],
  providers: [
    AuthService,
    UserService,
    AuthRoute,
    FolderService,
    DocService,
  ],
  bootstrap: [AppComponent],
})
export class AppModule { }
