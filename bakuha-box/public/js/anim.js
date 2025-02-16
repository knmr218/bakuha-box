let isAnimating = false;

async function startLabelAnimation(imgSrc) {
    if (isAnimating) {
        await new Promise(resolve => {
            const interval = setInterval(() => {
                if (!isAnimating) {
                    clearInterval(interval);
                    resolve();
                }
            }, 100);
        });
    }

    isAnimating = true;
    const container = document.querySelector('.label_container');
    const image = document.querySelector('.label_image');
    image.src = imgSrc;

    container.style.display = "flex";
    await new Promise(resolve => setTimeout(resolve, 100));
    container.style.opacity = 1;

    await new Promise(resolve => setTimeout(resolve, 2000));
    container.style.opacity = 0;

    await new Promise(resolve => setTimeout(resolve, 500));
    container.style.display = "none";

    isAnimating = false;
}
