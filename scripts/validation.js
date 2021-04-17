function t10_checkmail(strval,pesan) {

	if(valid) {

		var Pattern = new RegExp('[a-zA-Z][0-9a-zA-Z_/-]*[a-zA-Z][0-9a-zA-Z_.]*@[a-zA-Z][0-9a-zA-Z_/-]*(\\.[a-zA-Z][0-9a-zA-Z_]*)+$');

		Status = Pattern.test(strval.value);

		if (strval.value!=""){

			if (Status==false){

				valid = false;

				alert(pesan);

				strval.focus();

			}else{

				valid = true;

			}

		}else{

			valid = false;

			alert(pesan);

			strval.focus();

		}

	}

}



function t10_checkvaliddate(formdate,formmonth,formyear) {

	inputdate = formdate.value;

	inputmonth = formmonth.value;

	inputyear = formyear.value;



	t10_checkisi(formdate, 'Masukkan Date');

	t10_checkisi(formmonth, 'Masukkan Bulan');

	t10_checkisi(formyear, 'Masukkan Tahun');



	if(valid==1) {

		if(isNaN(inputdate)) {

			valid = 0;

			alert("Date yang anda masukkan salah");

			formdate.focus();

		}

	}



	if(valid==1) {

		if(isNaN(inputmonth)) {

			valid = 0;

			alert("Bulan yang anda masukkan salah");

			formmonth.focus();

		}

	}



	if(valid==1) {

		if(isNaN(inputyear)) {

			valid = 0;

			alert("Tahun yang anda masukkan salah");

			formyear.focus();

		}

	}



	if(valid==1) {

		var date = new Date();

		if((inputmonth<0)||(inputmonth>12)||(inputyear<1920)) {

			if ((inputmonth<0)||(inputmonth>12)) {

				valid = 0;

				alert("Bulan yang anda masukkan salah");

				formmonth.focus();

			}

			if ((inputyear<1920)) {

				if(valid==1) {

					valid = 0;

					alert("Tahun yang anda masukkan salah");

					formyear.focus();

				}	

			}

		}

		else {

			var count;

			if(valid==1) {

				if(inputmonth<8){

					if((inputmonth%2) == 1) count=31;

					if((inputmonth%2) == 0) count=30;

					if(inputmonth == 2) count=((inputyear%4==0)?29:28);

				}

				else{

					if((inputmonth-7)%2 == 1) count=31;

					if((inputmonth-7)%2 == 0) count=30;

				}

				if((inputdate<0)||(inputdate>count)) {

					valid = 0;

					alert("Date yang anda masukkan salah");

					formdate.focus();

				}

			}

		}

	}

}



function t10_checkisi(strval,pesan){ 
	if(valid) {

		if(strval.value=="") {

			valid = false;

			alert(pesan);

			strval.focus();

		}

	}

}

function t10_checknum(strval,pesan){ 

	if(valid) {

		if(isNaN(strval.value) || (strval.value=="")) {

			valid = false;

			alert(pesan);

			strval.focus();

		}

	}

}



function t10_checkrange(strval,pesan,r_awal,r_akhir){ 

	if(valid) {

		t10_checknum(strval,pesan);

		if(valid) {

			if((strval.value >= r_awal) && (strval.value <= r_akhir)) {

			}else{

				valid = false;

				alert(pesan);

				strval.focus();

			}

		}

	}

}

function t10_checkmaxlenght(strval,pesan,jumcar){

    if(valid){

        if (strval.value.length > jumcar){

            valid = false;

            alert('Isi '+pesan+' terlalu panjang, max '+jumcar+' karakter, isi data akan dipotong');

            strval.value = strval.value.substr(0, jumcar);
            
            strval.focus();

        }
        
    }

}
