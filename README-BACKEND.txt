===========================================================
 ADJISS — README FOR THE BACKEND DEVELOPER
===========================================================

Layunin ng file na ito: ipaliwanag kung ano ang inaasahan
ng frontend (index.html / style.css / script.js) galing sa
backend, para hindi kailangan basahin lahat ng JS para
malaman kung saan babagay ang PHP/MySQL (o kung anumang stack
ang gamitin niyo).

Sa ngayon, LAHAT ng data ay nasa isang JavaScript array lang
sa memory (`customers`, sa loob ng script.js) — nawawala ito
paglipas ng refresh. Wala pang totoong API call kahit saan.
Ito yung kailangang palitan.

-----------------------------------------------------------
 1. DATA MODEL — "customer" record
-----------------------------------------------------------
Ito yung shape ng bawat customer object sa frontend ngayon
(script.js, function na gumagawa ng bagong customer, line
~457 pataas):

    {
      id: number,             // auto-increment sa frontend ngayon (nextId++)
      fullName: string,
      email: string,
      contactNumber: string,  // format: 09XXXXXXXXX (11 digits, PH mobile)
      address: string,
      image: string,          // base64 data URL SA NGAYON (see section 4)
      createdAt: Date         // JS Date object; sa API dapat ISO string
    }

Iminumungkahing MySQL table (pwede baguhin ayon sa pangangailangan):

    CREATE TABLE customers (
      id              INT AUTO_INCREMENT PRIMARY KEY,
      full_name       VARCHAR(150)  NOT NULL,
      email           VARCHAR(150)  NOT NULL,
      contact_number  VARCHAR(15)   NOT NULL,
      address         VARCHAR(255)  NOT NULL,
      profile_image   VARCHAR(255)  NULL,   -- file path/URL, hindi base64
      created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

NOTE: sa response ng API, gamitin ang camelCase (fullName,
contactNumber, createdAt) PARA TUMUGMA sa inaasahan ng
frontend, o kung snake_case ang gusto niyo sa DB/API, mag-map
na lang sa isang lugar sa script.js (isang function lang ang
babaguhin, hindi kailangang buuin ulit).

-----------------------------------------------------------
 2. FORM FIELD IDs (index.html) — para sa reference
-----------------------------------------------------------
Customer form (#customerForm):
    #fullName        → name="full_name"
    #email           → name="email"
    #contactNumber   → name="contact_number"
    #address         → name="address"
    #profileImage    → name="profile_image"  (type="file")

Contact form (#contactForm) — walang backend endpoint pa,
kailangan niyo gawan ng sarili:
    #contactName
    #contactEmail
    #contactSubject
    #contactMessage  (max 500 characters, may client-side counter na)

-----------------------------------------------------------
 3. VALIDATION RULES (ALREADY enforced client-side —
    PERO KAILANGAN PA RIN I-VALIDATE ULIT SA BACKEND,
    huwag umasa sa frontend lang)
-----------------------------------------------------------
    Email:            /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    Contact Number:   /^09\d{9}$/   (11 digits total, nagsisimula sa "09")
    Full Name:        required, hindi pwedeng blangko
    Address:          required, hindi pwedeng blangko

Ang mga function na ito ay nasa script.js: isValidEmail(),
isValidPHContact() — kopyahin/i-mirror ang parehong logic sa
backend validation niyo (hal. PHP filter_var + regex) para
magkatugma ang error messages.

-----------------------------------------------------------
 4. PROFILE IMAGE UPLOAD — IMPORTANTE
-----------------------------------------------------------
Sa ngayon, ang #profileImage input ay ginagawang PREVIEW LANG
gamit ang FileReader.readAsDataURL() (script.js, "IMAGE
PREVIEW ON UPLOAD" section) — nagre-render siya bilang base64
string sa <img>, PERO WALANG UPLOAD NA NANGYAYARI KAHIT SAAN.
Kailangan niyo:

    1. Gawan ng endpoint na tumatanggap ng multipart/form-data
       (hal. POST /api/customers gamit ang FormData, hindi
       JSON, dahil may file).
    2. I-save ang file sa server (o cloud storage), tapos
       ibalik ang public URL/path bilang `profile_image` sa
       response — ito yung isa-store bilang `image` field sa
       frontend, hindi na base64.
    3. Kung walang na-upload na file, gamitin ang default:
       frontend already points to "images/default-profile.png"
       (see DEFAULT_IMAGE constant sa script.js, "CUSTOMER
       STATE" section).

-----------------------------------------------------------
 5. SUGGESTED REST ENDPOINTS
-----------------------------------------------------------
    GET    /api/customers            → list (support ?search=&sort=&dir=&page=)
    POST   /api/customers            → create (multipart/form-data)
    PUT    /api/customers/{id}       → update (multipart/form-data)
    DELETE /api/customers/{id}       → delete
    POST   /api/contact              → contact form submissions (bago, wala pa)

Ang search/sort/pagination ay ginagawa CLIENT-SIDE lang sa
ngayon (buong `customers` array laging nasa memory). Kung
malaki na ang dataset niyo paglaon, pwedeng ilipat sa backend
gamit ang query params sa itaas — pero optional lang ito, hindi
kailangan agad sa unang bersyon.

-----------------------------------------------------------
 6. WHERE TO PLUG IN THE API CALLS (script.js)
-----------------------------------------------------------
Hanapin ang mga sections na ito sa script.js — dito dapat
ipalit ang mga fetch() calls papalit sa in-memory array logic:

    "CUSTOMER STATE"              → `let customers = [];`
                                     → palitan ng fetch sa
                                       GET /api/customers pag
                                       nag-load ang page, tapos
                                       i-render pagdating ng data

    "ADD / UPDATE CUSTOMER"       → dito nangyayari ang
                                     customers.push(...) at
                                     customer.fullName = ...
                                     → palitan ng fetch POST/PUT,
                                       tapos i-refresh ang table
                                       gamit ang tunay na response
                                       (kasama ang totoong id at
                                       image URL galing sa server)

    "EDIT / DELETE / VIEW"        → openDeleteModal() / confirmDeleteBtn
                                     click handler → palitan ng
                                     fetch DELETE

    "CONTACT FORM"                → currently toast lang, walang
                                     saved kahit saan → palitan ng
                                     fetch POST /api/contact

Sa lahat ng palitan na ito, panatilihin ang parehong toast
notification pattern (showToast(...)) para consistent pa rin
ang UX — success/error state lang ang babaguhin base sa response.

-----------------------------------------------------------
 7. CSV EXPORT / PRINT
-----------------------------------------------------------
Client-side generated lang ito galing sa kasalukuyang laman ng
table (walang backend dependency, script.js "exportCsvBtn"
section). Columns order: ID, Full Name, Email, Contact Number,
Address. Hindi na kailangan gawan ng backend endpoint maliban
kung gusto niyo ng server-side export/report generation sa
hinaharap.

-----------------------------------------------------------
 8. SECURITY NOTES
-----------------------------------------------------------
 - May escapeHtml() na ang frontend bago i-render ang customer
   data sa table (para maiwasan ang XSS sa display), pero HINDI
   ito kapalit ng backend-side sanitization/validation — i-treat
   pa rin bilang untrusted lahat ng papasok sa API.
 - I-validate ulit sa server side ang email format at contact
   number bago i-save sa DB (huwag basta i-trust yung nakapasa
   na sa frontend validation).
 - I-restrict ang file upload type/size sa backend (accept
   lang image/*, ilagay ng max file size) — ang "accept=image/*"
   sa frontend ay UI hint lang, hindi totoong security control.

===========================================================
 Tapos na ang gabay na ito. Kung may tanong sa specific na
 part ng frontend code, tingnan ang mga comment sa loob ng
 script.js — sinadya itong ginawang detalyado (Taglish pa
 nga) para madali basahin ng buong team.
===========================================================
