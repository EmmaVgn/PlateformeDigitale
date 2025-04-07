import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  connect() {
    console.log("🎯 test_controller utilisé comme quiz")
  }
}
