export default class ScatterController extends DatasetController {
    static id: string;
    /**
     * @type {any}
     */
    static overrides: any;

    update(mode: any): void;

    /**
     * @protected
     */
    protected getLabelAndValue(index: any): {
        label: any;
        value: string;
    };

    /**
     * @protected
     */
    protected getMaxOverflow(): any;
}
import DatasetController from "../core/core.datasetController.js";
