# VersionCentral

## Support

**TYPO3 Version**

* Tested versions: 7.6.4
* If you run any other versions, just try it ;-)

## Installation

### TYPO3 Extension Manager

Download the [current ZIP](https://github.com/version-central/ext-typo3/releases/download/v1.0.0/versioncentral_1.0.0.zip) and install the ZIP file via the TYPO3 Extension Manager in your backend at Admin Tools > Extensions.

## Configuration

Open the Scheduler in the TYPO3 backend at System > Scheduler. Add a new task and select the class `VersionCentral Updater`.

Set the `Frequency` field to run at least daily (e.g. with 86400 seconds).

Enter your access token in the `Credentials` field and save the task.

You can force a first run of the task from the TYPO3 scheduler to initialize your VersionCentral project.

**Note:** The TYPO3 cronjob runner has to be configured and active. See the [documentation](https://docs.typo3.org/typo3cms/extensions/scheduler/Installation/CronJob/Index.html) for more information on this.

## Contact

If you have any problems or suggestions, just email us at [support@versioncentral.com](mailto:support@versioncentral.com).
