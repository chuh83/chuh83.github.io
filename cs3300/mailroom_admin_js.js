function sendEmail(name, email, packageLocation) {
	const d = new Date()
	const dtf = new Intl.DateTimeFormat('en', { year: 'numeric', month: 'short', day: '2-digit' }) 
	const [{ value: mo },,{ value: da },,{ value: ye }] = dtf.formatToParts(d) 
	var date = `${da}-${mo}-${ye}`;
	var sendString = "" + name + ",<br><br>Your Package Has Arrived!<br><br><b>Package Details:</b><br>Date Recieved: " + date + "<br>Signed For By:<br>PackageLocation: " + packageLocation;
	sendString = sendString + "<br><br><br><br>Please come to Busch Student Center room 129 to retrieve your package. It will not be delivered. Please see the hours of operations listed below.<br><br>You must pick up your package within 15 days. Please bring your SLU student I.D. or another government issued photo identification.<br>";
	sendString = sendString + "Do not reply to this email. You may contact Student Mail Services at (314) 977-1128 or slumailroom@slu.edu.<br><br><br><br>Student Mail Services<br>Busch Student Center Room 129<br>314.977.1128<br><br><b>Academic Hours: </b><br>M-Th: 9:00AM-6:00PM<br>Friday:9:00AM-5:00PM<br>Sat.: 10:00AM - 1:00PM<br>Sun: CLOSED"
	//console.log(sendString);
	//console.log("Email Sent");
	
	/*Email.send({
	Host: "smtp.gmail.com",
	Username : "mailroommailer@gmail.com",
	Password : "INSERT PASSWORD",
	To : email,
	From : "mailroommailer@gmail.com",
	Subject : "Package Notification",
	Body : sendString,
	})*/
	
	Email.send({
	SecureToken : "1c4a21ad-92ff-4825-b49e-e00c34e0721e",
	To : email,
	From : "mailroommailer@gmail.com",
	Subject : "Package Notification",
	Body : sendString,
	})
	
	
	/*
	*/
}

function sendReminder(name, email) {
	var sendString = "" + name + ",<br><br>Your Package is Still Waiting!<br><br>";
	sendString = sendString + "<br>Please come to Busch Student Center room 129 to retrieve your package. It will not be delivered. Please see the hours of operations listed below.<br><br>You must pick up your package within 15 days of recieving the original package notification email. Please bring your SLU student I.D. or another government issued photo identification.<br>";
	sendString = sendString + "Do not reply to this email. You may contact Student Mail Services at (314) 977-1128 or slumailroom@slu.edu.<br><br><br><br>Student Mail Services<br>Busch Student Center Room 129<br>314.977.1128<br><br><b>Academic Hours: </b><br>M-Th: 9:00AM-6:00PM<br>Friday:9:00AM-5:00PM<br>Sat.: 10:00AM - 1:00PM<br>Sun: CLOSED"
	//console.log(sendString);
	//console.log("Email Sent");
	
	/*Email.send({
	Host: "smtp.gmail.com",
	Username : "mailroommailer@gmail.com",
	Password : "INSERT PASSWORD",
	To : email,
	From : "mailroommailer@gmail.com",
	Subject : "Package Reminder",
	Body : sendString,
	})*/
	
	
	Email.send({
	SecureToken : "1c4a21ad-92ff-4825-b49e-e00c34e0721e",
	To : email,
	From : "mailroommailer@gmail.com",
	Subject : "Package Reminder",
	Body : sendString,
	})
	
}

/* SmtpJS.com - v3.0.0 */
var Email = {
    send: function(a) {
        return new Promise(function(n, e) {
            a.nocache = Math.floor(1e6 * Math.random() + 1),
            a.Action = "Send";
            var t = JSON.stringify(a);
            Email.ajaxPost("https://smtpjs.com/v3/smtpjs.aspx?", t, function(e) {
                n(e)
            })
        }
        )
    },
    ajaxPost: function(e, n, t) {
        var a = Email.createCORSRequest("POST", e);
        a.setRequestHeader("Content-type", "application/x-www-form-urlencoded"),
        a.onload = function() {
            var e = a.responseText;
            null != t && t(e)
        }
        ,
        a.send(n)
    },
    ajax: function(e, n) {
        var t = Email.createCORSRequest("GET", e);
        t.onload = function() {
            var e = t.responseText;
            null != n && n(e)
        }
        ,
        t.send()
    },
    createCORSRequest: function(e, n) {
        var t = new XMLHttpRequest;
        return "withCredentials"in t ? t.open(e, n, !0) : "undefined" != typeof XDomainRequest ? (t = new XDomainRequest).open(e, n) : t = null,
        t
    }
};
