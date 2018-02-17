import { Component,Input } from '@angular/core';


declare var $: any;


@Component({
  selector: 'infos',
  templateUrl:'./infos.html',
  styleUrls: [],
})

export class Infos {
  @Input() id;
  @Input() link;
  @Input() added;
  @Input() name;
  @Input() description;
  dest;
  modal;

  ngOnInit() {
    this.dest = "#File"+this.id;
    this.modal = "File"+this.id;
  }

  showModal(dest){
    $(dest).modal('show');
  }
  closeModal(dest){
    $(dest).modal('hide');
  }
}
