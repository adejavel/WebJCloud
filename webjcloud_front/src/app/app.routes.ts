import { ModuleWithProviders }  from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CanActivate } from '@angular/router';



import { AuthRoute } from "./route.auth";

import {Login} from './components/login/login';
import {Dossiers} from './components/dossiers/dossiers';
import {Header} from'./components/header/header';
import {Years} from './components/years/years';
import {Dossier} from './components/dossier/dossier'
import {Valid} from './components/valid/valid'
import {Diaporama} from "./components/diaporama/diaporama";
import {ValidAdmin} from "./components/valid/validadmin";
import { Resolve } from '@angular/router';

const appRoutes: Routes = [

  { path: 'login', component: Login },
  { path: 'valid/user/:key', component: Valid },
  { path: 'valid/admin/:key', component: ValidAdmin,canActivate:[AuthRoute] },
  { path: ':year/:dossier/:doc',component:Diaporama,canActivate:[AuthRoute]},
  {
    path: '',
    component:Header,
    canActivateChild: [AuthRoute],
    children: [
      { path: '', component: Years },
      { path: ':year', component: Dossiers },
      { path: ':year/:dossier', component: Dossier },

      ]
  },
  { path: '**', redirectTo: '' },

];

export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);
