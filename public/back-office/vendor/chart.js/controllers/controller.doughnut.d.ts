export default class DoughnutController extends DatasetController {
    static id: string;
    static descriptors: {
        _scriptable: (name: any) => boolean;
        _indexable: (name: any) => boolean;
    };
    /**
     * @type {any}
     */
    static overrides: any;
    innerRadius: number;
    outerRadius: number;
    offsetX: number;
    offsetY: number;
    /**
     * @private
     */
    private _getRotation;
    /**
     * @private
     */
    private _getCircumference;
    /**
     * @private
     */
    private _circumference;
    /**
     * Get radius length offset of the dataset in relation to the visible datasets weights. This allows determining the inner and outer radius correctly
     * @private
     */
    private _getRingWeightOffset;
    /**
     * @private
     */
    private _getRingWeight;
    /**
     * Returns the sum of all visible data set weights.
     * @private
     */
    private _getVisibleDatasetWeightTotal;

    constructor(chart: any, datasetIndex: any);

    /**
     * Override data parsing, since we are not using scales
     */
    parse(start: any, count: any): void;

    /**
     * Get the maximal rotation & circumference extents
     * across all visible datasets.
     */
    _getRotationExtents(): {
        rotation: number;
        circumference: number;
    };

    calculateTotal(): number;

    calculateCircumference(value: any): number;

    getLabelAndValue(index: any): {
        label: any;
        value: string;
    };

    getMaxBorderWidth(arcs: any): number;

    getMaxOffset(arcs: any): number;
}
export type Chart = import('../core/core.controller.js').default;
import DatasetController from "../core/core.datasetController.js";
