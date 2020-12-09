/*ENV variables for GOOGLE ANALYTICS*/
const GOOGLE_TAG_MANAGER_ID = process.env.GOOGLE_TAG_MANAGER_ID;

window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', GOOGLE_TAG_MANAGER_ID);