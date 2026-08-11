===========================================================
 ADJISS — Customer Information Management System (Frontend)
===========================================================

AJ, Donna, Justin Integrated Systems Solutions. Ito yung
frontend ng CIMS niyo — pure HTML, CSS, at Vanilla JavaScript
pa rin, wala pang backend (extra README para doon, see
README-BACKEND.txt).

-----------------------------------------------------------
 1. FOLDER STRUCTURE
-----------------------------------------------------------
ADJISS/
│ index.html            → structure ng buong site
│ style.css             → design system (Corporate Navy Blue theme)
│ script.js             → lahat ng interactivity
│ README.txt            → itong file
│ README-BACKEND.txt    → guide para sa gagawa ng backend
│
└── images/
    │ logo.png              → ADJISS logo mark
    │ default-profile.png   → default avatar (walang uploaded photo)
    │ hero.png              → Home section background
    │ customers.png         → Customer System section background
    │ about.png             → About section background
    │ contact.png           → Contact section background (BAGO,
    │                          dati unused sa folder niyo, ginamit
    │                          ko na — same pattern gaya ng hero/
    │                          customers/about)

WALANG NAGBAGO SA FILENAMES — pwede mo direktang i-drag ang
sarili niyong logo.png / hero.png / atbp. papalit dito, same
filename, at automatic na gagana, walang babaguhing code.
(Ang mga laman ng /images ngayon ay generated placeholder art
lang — navy-blue "network/node" pattern, para may laman habang
wala pa yung totoong photos/logo niyo.)

-----------------------------------------------------------
 2. BASE FEATURES (galing na sa orihinal niyong file)
-----------------------------------------------------------
 - Hero section, "Why Choose ADJISS" cards, About (Mission/
   Vision), Contact form, Footer
 - Full Customer CRUD: Add / Edit / Delete (with confirm modal)
 - Live search (header search + dedicated customer search)
 - Sortable table columns (click ID/Name/Email/Contact/Address)
 - Pagination
 - CSV export + Print
 - Client-side form validation (email format, PH mobile
   number format 09XXXXXXXXX, required fields)
 - Profile image upload with live preview
 - Dark / Light mode toggle
 - Toast notifications

-----------------------------------------------------------
 3. NEW FEATURES ADDED (this update)
-----------------------------------------------------------

 1. ACCENT COLOR SWITCHER
    4 dots sa navbar (Ocean Blue / Emerald / Indigo / Magenta)
    sa tabi ng dark/light toggle — pinapalitan nito ang accent
    color ng BUONG site nang live (mga buttons, borders, active
    nav underline, table sort highlight, scroll-to-top button,
    lahat). Gumagana kasabay ng dark/light mode — naa-adjust
    yung shade para readable pa rin sa white background pag
    light mode. Naka-save sa localStorage.

 2. CLICK-TO-EXPAND DETAIL CARDS
    Yung 3 cards sa Hero ("Trusted Business Solutions", "Secure
    Data Management", "Fast & Reliable Systems"), yung 3
    "Why Choose ADJISS" cards, at yung Mission/Vision cards —
    same hover behavior pa rin, pero ngayon pwede mo ring
    i-click (o Enter/Space kung naka-keyboard navigation) para
    makita ang mas detalyadong paliwanag sa isang modal. Isang
    reusable modal lang ang ginamit dito (#detailModal) — kung
    gusto mo magdagdag pa ng bagong detail card, tingnan yung
    "DETAILS" object sa taas ng script.js.

 3. CUSTOMER "VIEW DETAILS" MODAL
    Bagong "View" button sa bawat row ng customer table
    (bukod sa Edit/Delete) — nagpapakita ng buong profile sa
    isang modal (malaking avatar, ID, email, contact, address,
    petsa nang idinagdag) nang hindi kailangan pumasok sa Edit
    mode. May "Edit This Customer" button din dito na
    direktang lilipat sa edit form kung kailangan.

 4. ACTIVE NAVIGATION HIGHLIGHT
    Habang nag-sscroll ka, awtomatikong may underline/highlight
    ang nav link ng section na kasalukuyang nakikita
    (Home / Customers / About / Contact).

 5. SCROLL TO TOP BUTTON
    Lumalabas pag medyo malayo na ang scroll, one-click pabalik
    sa taas.

 6. LOADING SCREEN
    Mabilis na "ADJISS" splash (under 1 segundo) habang
    naglo-load ang background images, para walang nakikitang
    flash bago mag-apply ang tema.

 7. "ADDED TODAY" REPORT CARD
    Bukod sa "Total Customers", may bagong card na nagbibilang
    kung ilang customer ang na-add ngayong araw — gamit ang
    bagong `createdAt` field na naka-attach na sa bawat bagong
    customer.

 8. CONTACT MESSAGE CHARACTER COUNTER
    Live na "123 / 500" counter sa ilalim ng message textarea,
    para may guide ang user kung gaano na kahaba ang sinusulat.

 9. "/" KEYBOARD SHORTCUT
    Pindutin ang "/" kahit saan sa page (habang hindi naka-type
    sa ibang field) para direktang mapunta ang focus sa header
    search box — karaniwang shortcut sa mga totoong admin tools.

-----------------------------------------------------------
 4. IDEAS FOR NEXT ITERATION (frontend-focused, not yet built)
-----------------------------------------------------------
 - Bulk select + bulk delete sa customer table
 - Column visibility toggle (itago/ipakita ang Address column, atbp.)
 - "Undo" toast pagkatapos mag-delete (5 seconds bago talagang
   mawala ang record)
 - Advanced filter (hal. customers na walang laman ang address)
 - Skeleton loading rows habang naglo-load ang table mula sa
   backend (importante ito once may real API na kayo)
 - Drag-and-drop profile image upload (bukod sa click-to-browse)
 - Recently viewed customers strip sa itaas ng table

-----------------------------------------------------------
 5. CODE QUALITY
-----------------------------------------------------------
 - Vanilla JS lang, walang dependencies
 - Na-syntax-check (node --check) — walang errors
 - Lahat ng bagong section may comment header, Taglish para
   mas madaling intindihin ng buong team
 - Reusable functions imbes na paulit-ulit na code (hal.
   enterEditMode() ginagamit parehas ng Edit button at ng
   "Edit This Customer" sa View modal)
 - Lahat ng accent colors nasa CSS variables na — walang
   hardcoded hex color sa loob ng mga component

-----------------------------------------------------------
 6. HOW TO CUSTOMIZE
-----------------------------------------------------------
 - Palitan ang mga kulay: buksan ang style.css, hanapin ang
   ":root { ... }" sa taas — doon nakadefine ang --accent,
   --accent-hover, --accent-active, --accent-rgb. Kung gusto
   mo magdagdag ng 5th accent option sa switcher, kopyahin
   lang yung "body[data-accent=...]" block pattern.
 - Palitan ang mga detail card content: script.js, hanapin ang
   "const DETAILS = {" object.
 - Palitan ang mga images: palitan lang ang files sa /images
   gamit ang parehong filename.

Enjoy building, AJ, Donna, Justin! 🔧💼
