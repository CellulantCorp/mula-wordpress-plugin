'use strict';

const {Stepper, Step, StepLabel, StepContent, Button, Paper, Typography} = window['material-ui'];

const styles = {
    footer: {padding: '24px'},
    label: {backgroundColor: '#35cbf8'},
    button: {color: '#ffffff', backgroundColor: '#35cbf8'},
};

function getSteps() {
    return ['Registration', 'Setup a service', 'Connect to WordPress'];
}

function getStepContent(step) {
      switch (step) {
          case 0:
              return `<p>In order to use the Mula WordPress plugin, one needs to register an account with Mula.</p>
                  <p>This can be done in these easy steps:</p>
                  <ol>
                      <li>Register on this page</li>
                      <li>Confirm your email address</li>
                      <li>Log into your merchant dashboard</li>
                  </ol>`;
          case 1:
              return `<p>Create a service with a descriptive name. And select all payment options you would like associated with the service.</p>
                  <strong>NB:</strong> <span>If the select button in orange, that payment option is not ready for use. After it turns blue, it then means it would be displayed to your customers during checkout.</span>`;
          case 2:
              return `<p>All that remains is to import your Mula details to your newly installed WordPress plugin.</p>
                  <p>Click on the configurations sub-menu and ensure that these fields are accurately filled:</p>
                  <ol>
                       <li>IV key</li>
                       <li>Secret key</li>
                       <li>Access key</li>
                       <li>Service code</li>
                       <li>Payment period</li>
                       <li>Default country</li>
                  </ol>`;
          default:
              return 'Unknown step';
      }
}

class GetStartedStepper extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            activeStep: 0
        };
    }

    handleNext = () => {
        this.setState(state => ({
            activeStep: state.activeStep + 1,
        }));
    };

    handleBack = () => {
        this.setState(state => ({
            activeStep: state.activeStep - 1,
        }));
    };

    handleReset = () => {
        this.setState({
            activeStep: 0,
        });
    };

    render() {
        const steps = getSteps();
        const { activeStep } = this.state;

        return (<div>
            <Stepper activeStep={activeStep} orientation="vertical">
                  {steps.map((label, index) => (
                        <Step key={label}>
                              <StepLabel>{label}</StepLabel>
                              <StepContent>
                                  <Typography component={'div'}>
                                      <p dangerouslySetInnerHTML={{ __html: getStepContent(index)}}/>
                                  </Typography>
                                  <div>
                                      <div>
                                          <Button disabled={activeStep === 0} onClick={this.handleBack}>Back</Button>
                                            <Button variant="contained" style={styles.button} onClick={this.handleNext}>
                                                {activeStep === steps.length - 1 ? 'Finish' : 'Next'}
                                            </Button>
                                      </div>
                                  </div>
                              </StepContent>
                        </Step>
                  ))}
            </Stepper>
            {activeStep === steps.length && (
            <Paper style={styles.footer} square elevation={0}>
                <Typography>All steps completed - you&apos;re finished</Typography>
                <br/>
                <Button style={styles.button} onClick={this.handleReset}>Re-read the steps</Button>
            </Paper>
        )}
        </div>);
    }
}

ReactDOM.render(<GetStartedStepper/>, document.getElementById('mula-plugin-get-started-guide'));