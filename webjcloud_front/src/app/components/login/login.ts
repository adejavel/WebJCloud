import { Component } from "@angular/core";
import { Router } from "@angular/router";
import { UserService } from "../../services/user.service";

@Component({
  templateUrl:'./login.html',
  styleUrls: ['./login.css'],
})
export class Login  {
  username: string;
  password: string;
  error: boolean;
  loading: boolean;
  text_error="";
  text_info="";
  login=true;
  signin=false;
  firstname;
  lastname;
  email;
  emailRep;
  plainPassword;
  plainPasswordRep;
  texte="S'inscrire";

  constructor(private _user: UserService, private router: Router) {
  }

  onSubmit(event) {
    this.text_error="";
    event.preventDefault();
    this.error = false;
    this.loading = true;
    this._user.login(this.username, this.password).subscribe(
      response => {
        localStorage.setItem('auth_token', response.json().token);
        localStorage.setItem('valid', response.json().valid);
        this._user.loggedIn = true;
        this.router.navigate(['']);
      },
      error => {
        setTimeout(() => {
          this.loading = false;
          this.error = true;
        }, 500);
        if (error.json().message==='account not valid'){
          this.error=false;
          this.text_error="Votre compte n'est pas encore valide. Suite à votre inscription, vous avez du recevoir un mail de confirmation et un lien pour valider votre compte. Suite à cette première validation, l'administrateur a été averti de votre inscription et validera ce compte prochainement ! ";
        }

        localStorage.removeItem('auth_token');
        localStorage.removeItem('sw_token');
      }
    );
  }
  onSubmitSignin(event) {
    this.text_error="";
    if ((this.email === this.emailRep) && (this.plainPassword === this.plainPasswordRep) && (this.firstname !== "") && (this.lastname !== "" )&& (this.email !== "") &&( this.plainPassword !== "")) {
      let data = {
        firstname: this.firstname,
        lastname: this.lastname,
        email: this.email,
        plainPassword: this.plainPassword
      };
      console.log(data);
      this._user.signin(data).subscribe(
        response => {
          this.switch();
          this.text_info = "Votre inscription a été prise en compte, vous allez recevoir un mail contenant un lien vous permetant de l'activer !";
        },
        error => {
          if (error.json().message === 'User already existing') {
            this.text_error += "Désolé, un utilisateur avec la même adresse email existe déjà";

          }
          else if(error.json().message==='mail invalide!'){
            this.text_error+="Désolé, l'adresse email spécifiée est invalide !"
          }
        }
      )

    }
    else {
      this.text_error="Le mot de passe ou l'adresse mail est incorrecte"
    }
  }

  switch(){
    if (this.login){
      this.login=false;
      this.signin=true;
      this.texte="Se connecter";
    }
    else {
      this.login=true;
      this.signin=false;
      this.texte="S'inscrire";
    }
  }

}

