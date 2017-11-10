
Actions:
  getContactInfo
    input: a=id en b=hash
    output: info in json formaat of error
  updateContactInfo
    input: a=id en b=hash + velden in POST
    output: OK of error

  sendContactLink
    input: email=e-mailadres
      stuurt een mail naar het opgegeven e-mailadres
      Indien het e-mailadres nog niet bestaat, wordt het aangemaakt en wordt een statusveld op "pending" gezet.
      Indien het een nieuw contact is, is de tekst anders dan voor een bestaand contact.
      In de mail zit ook een unieke link naar het contact. (id en hash)
      E-mails zijn sjablonen in civicrm.
    output: OK of error

   
Token aanmaken voor de unieke link naar het contact:
  contactlink

OptionGroup aanmaken: ContactProfile
OptionValue aanmaken: ProfilePageLink

3 berichtsjablonen aanmaken:
  nieuwe registratie
  update gegevens
  meerdere contacten gevonden met dat e-mailadres

