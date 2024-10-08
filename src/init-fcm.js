import { getMessaging } from "firebase/messaging";

import { initializeApp } from "firebase/app";

let messaging;

if ('Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window) {
	if (
		localStorage.getItem("firebasePublic") !== "null" &&
		localStorage.getItem("firebaseSenderId") !== "null" &&
		localStorage.getItem("firebasePublic") !== null &&
		localStorage.getItem("firebaseSenderId") !== null
	) {
		const initializedFirebaseApp = initializeApp({
			// const firebaseConfig = {
				apiKey: "AIzaSyDzcyfGaONpPa7bDPhG85-sQpWxhddqAZ0",
				authDomain: "sommelier-59a45.firebaseapp.com",
				projectId: "sommelier-59a45",
				storageBucket: "sommelier-59a45.appspot.com",
				messagingSenderId: "637835596399",
				appId: "1:637835596399:web:7d042c146557021155f39a",
				measurementId: "G-EZDWV7FC7P"
			//   };
		});
		messaging = getMessaging(initializedFirebaseApp);
		// messaging.usePublicVapidKey(localStorage.getItem("firebasePublic"));
	} else {
		const initializedFirebaseApp = initializeApp({
			// 			const firebaseConfig = {
				apiKey: "AIzaSyDzcyfGaONpPa7bDPhG85-sQpWxhddqAZ0",
				authDomain: "sommelier-59a45.firebaseapp.com",
				projectId: "sommelier-59a45",
				storageBucket: "sommelier-59a45.appspot.com",
				messagingSenderId: "637835596399",
				appId: "1:637835596399:web:7d042c146557021155f39a",
				measurementId: "G-EZDWV7FC7P"
			// };
		});
		messaging = getMessaging(initializedFirebaseApp);
		//  messaging.usePublicVapidKey(
		// 	"BH5L8XrGsNpki5uF1008FbZzgKKZN-NmhOwdWL5Lxh5r3nsgZ6N_Dged1IYXXCCJwpnrXzs52G_v3vM_naO0hxY"
		// ); 
	}
}
export default messaging;
