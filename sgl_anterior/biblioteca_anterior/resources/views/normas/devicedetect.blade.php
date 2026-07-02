    // Device detection ---------------
    var browserDevice = null;
    var detectDevice = function () {
        return {
            device: !!navigator.maxTouchPoints ? 'mobile' : 'computer',
            orientation: !navigator.maxTouchPoints ? 'desktop' : !window.screen.orientation.angle ? 'portrait' : 'landscape'
        };
    };    