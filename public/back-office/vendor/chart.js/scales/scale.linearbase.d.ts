export default class LinearScaleBase extends Scale {
    /** @type {number} */
    start: number;
    /** @type {number} */
    end: number;
    /** @type {number} */
    _startValue: number;
    /** @type {number} */
    _endValue: number;
    _valueRange: number;

    parse(raw: any, index: any): number;

    handleTickRangeOptions(): void;

    getTickLimit(): number;

    getLabelForValue(value: any): string;

    /**
     * @protected
     */
    protected computeTickLimit(): number;
}
import Scale from "../core/core.scale.js";
