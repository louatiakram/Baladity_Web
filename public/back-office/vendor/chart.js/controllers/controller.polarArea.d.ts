export default class PolarAreaController extends DatasetController {
    static id: string;
    /**
     * @type {any}
     */
    static overrides: any;
    innerRadius: number;
    outerRadius: number;
    /**
     * @private
     */
    private _updateRadius;
    /**
     * @private
     */
    private _computeAngle;

    constructor(chart: any, datasetIndex: any);

    getLabelAndValue(index: any): {
        label: any;
        value: string;
    };

    parseObjectData(meta: any, data: any, start: any, count: any): {
        r: unknown;
    }[];

    update(mode: any): void;

    countVisibleElements(): number;

    /**
     * @protected
     */
    protected getMinMax(): {
        min: number;
        max: number;
    };
}
import DatasetController from "../core/core.datasetController.js";
