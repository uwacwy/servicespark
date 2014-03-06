/*
	mustaches.js
*/

var mustaches = {
	address : '<div class="address"><div class="row"><div class="col-md-12"><div class="form-group"><label for="Address{{idx}}Type">Address Type</label><select class="form-control" id="Address{{idx}}Type" name="data[Address][{{idx}}][type]"><option value="both">Physical and Mailing</option><option value="mailing">Mailing Address</option><option value="physical">Physical Address</option></select></div></div></div><div class="row"><div class="col-md-12"><div class="form-group"><label for="Address{{idx}}Address1">Address 1</label><input id="Address{{idx}}Address1" type="text" name="data[Address][{{idx}}][address1]" class="form-control"></div></div></div><div class="row"><div class="col-md-12"><div class="form-group"><label for="Address{{idx}}Address2">Address 2</label><input id="Address{{idx}}Address2" type="text" name="data[Address][{{idx}}][address2]" class="form-control"></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label for="Address{{idx}}City">City</label><input id="Address{{idx}}City" type="text" name="data[Address][{{idx}}][city]" class="form-control"></div></div><div class="col-md-3"><div class="form-group"><label for="Address{{idx}}State">State</label><input id="Address{{idx}}State" type="text" name="data[Address][{{idx}}][state]" class="form-control"></div></div><div class="col-md-3"><div class="form-group"><label for="Address{{idx}}Zip">Zip Code</label><input id="Address{{idx}}Zip" type="text" name="data[Address][{{idx}}][zip]" class="form-control"></div></div></div> <hr> </div>'
};