export default class Animations {
    _chart: any;
    _properties: Map<any, any>;
    /**
     * Utility to handle animation of `options`.
     * @private
     */
    private _animateOptions;
    /**
     * @private
     */
    private _createAnimations;

    constructor(chart: any, config: any);

    configure(config: any): void;

    /**
     * Update `target` properties to new values, using configured animations
     * @param {object} target - object to update
     * @param {object} values - new target properties
     * @returns {boolean|undefined} - `true` if animations were started
     **/
    update(target: object, values: object): boolean | undefined;
}
