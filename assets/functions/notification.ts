let audio: HTMLAudioElement = null;

/**
 * Emet un son lors d'une notification
 */
export const playNotification = () => {
    audio = new Audio('/notification.mp3');
    audio.volume = 0.5;
    audio.play();
}